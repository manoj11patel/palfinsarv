<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_create_and_submit_application_for_owned_customer(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $customer = Customer::create([
            'agent_user_id' => $agent->id,
            'full_name' => 'Demo Customer',
            'phone' => '1111111111',
            'email' => 'demo-customer@example.com',
            'status' => 'draft',
        ]);
        $product = Product::create([
            'name' => 'Personal Loan',
            'code' => 'PL-1',
            'is_active' => true,
        ]);

        Sanctum::actingAs($agent);

        $createResponse = $this->postJson('/api/v1/applications', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'profile_payload' => ['income' => 50000],
        ]);

        $createResponse->assertCreated();
        $applicationId = $createResponse->json('id');

        $submitResponse = $this->postJson("/api/v1/applications/{$applicationId}/submit");
        $submitResponse->assertOk()->assertJsonPath('status', 'submitted');
    }

    public function test_agent_cannot_create_application_for_other_agents_customer(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $anotherAgent = User::factory()->create(['role' => 'agent']);

        $foreignCustomer = Customer::create([
            'agent_user_id' => $anotherAgent->id,
            'full_name' => 'Foreign Customer',
            'phone' => '2222222222',
            'email' => 'foreign-customer@example.com',
            'status' => 'draft',
        ]);

        $product = Product::create([
            'name' => 'Car Loan',
            'code' => 'CL-1',
            'is_active' => true,
        ]);

        Sanctum::actingAs($agent);

        $response = $this->postJson('/api/v1/applications', [
            'customer_id' => $foreignCustomer->id,
            'product_id' => $product->id,
        ]);

        $response->assertForbidden();
    }

    public function test_agent_can_upload_document_for_owned_application(): void
    {
        Storage::fake('public');

        $agent = User::factory()->create(['role' => 'agent']);
        $customer = Customer::create([
            'agent_user_id' => $agent->id,
            'full_name' => 'Upload Customer',
            'phone' => '3333333333',
            'email' => 'upload-customer@example.com',
            'status' => 'draft',
        ]);
        $product = Product::create([
            'name' => 'Business Loan',
            'code' => 'BL-1',
            'is_active' => true,
        ]);
        $application = Application::create([
            'customer_id' => $customer->id,
            'agent_user_id' => $agent->id,
            'product_id' => $product->id,
            'status' => 'draft',
        ]);

        Sanctum::actingAs($agent);

        $response = $this->postJson('/api/v1/documents', [
            'application_id' => $application->id,
            'document_type' => 'identity_proof',
            'file' => UploadedFile::fake()->create('id-proof.pdf', 100, 'application/pdf'),
        ]);

        $response->assertCreated();

        $document = Document::first();
        $this->assertNotNull($document);
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_admin_can_verify_and_convert_submitted_application(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'agent']);

        $customer = Customer::create([
            'agent_user_id' => $agent->id,
            'full_name' => 'Lifecycle Customer',
            'phone' => '4444444444',
            'email' => 'lifecycle-customer@example.com',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $product = Product::create([
            'name' => 'Gold Loan',
            'code' => 'GL-1',
            'is_active' => true,
        ]);

        $application = Application::create([
            'customer_id' => $customer->id,
            'agent_user_id' => $agent->id,
            'product_id' => $product->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        $verifyResponse = $this->postJson("/api/v1/applications/{$application->id}/verify");
        $verifyResponse->assertOk()->assertJsonPath('status', 'verified');

        $convertResponse = $this->postJson("/api/v1/applications/{$application->id}/convert");
        $convertResponse->assertOk()->assertJsonPath('status', 'converted');
    }

    public function test_customer_can_submit_public_onboarding_with_token(): void
    {
        Storage::fake('public');

        $agent = User::factory()->create(['role' => 'agent']);
        $product = Product::create([
            'name' => 'Home Loan',
            'code' => 'HL-1',
            'is_active' => true,
        ]);

        Sanctum::actingAs($agent);

        $linkCreateResponse = $this->postJson('/api/v1/onboarding-links', [
            'expires_at' => now()->addDay()->toIso8601String(),
        ]);

        $linkCreateResponse->assertCreated();
        $token = $linkCreateResponse->json('token');

        $submitResponse = $this->postJson("/api/v1/onboarding/{$token}/submit", [
            'full_name' => 'Token Customer',
            'phone' => '5555555555',
            'email' => 'token-customer@example.com',
            'product_id' => $product->id,
            'profile_payload' => ['city' => 'Mumbai'],
            'documents' => [
                [
                    'document_type' => 'identity_proof',
                    'file' => UploadedFile::fake()->create('identity.pdf', 120, 'application/pdf'),
                ],
            ],
        ]);

        $submitResponse->assertCreated()
            ->assertJsonPath('application.status', 'submitted')
            ->assertJsonPath('customer.status', 'submitted');

        $document = Document::where('document_type', 'identity_proof')->first();
        $this->assertNotNull($document);
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_onboarding_token_cannot_be_reused_after_submission(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $product = Product::create([
            'name' => 'Secure Loan',
            'code' => 'SL-1',
            'is_active' => true,
        ]);

        Sanctum::actingAs($agent);
        $linkResponse = $this->postJson('/api/v1/onboarding-links', [
            'expires_at' => now()->addDay()->toIso8601String(),
        ]);
        $token = $linkResponse->json('token');

        $firstSubmit = $this->postJson("/api/v1/onboarding/{$token}/submit", [
            'full_name' => 'First Submit',
            'phone' => '6666666666',
            'email' => 'first-submit@example.com',
            'product_id' => $product->id,
        ]);
        $firstSubmit->assertCreated();

        $secondSubmit = $this->postJson("/api/v1/onboarding/{$token}/submit", [
            'full_name' => 'Second Submit',
            'phone' => '7777777777',
            'email' => 'second-submit@example.com',
            'product_id' => $product->id,
        ]);
        $secondSubmit->assertStatus(409);
    }

    public function test_admin_can_review_documents(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'agent']);
        $customer = Customer::create([
            'agent_user_id' => $agent->id,
            'full_name' => 'Doc Review Customer',
            'phone' => '8888888888',
            'email' => 'doc-review@example.com',
            'status' => 'submitted',
        ]);

        $product = Product::create([
            'name' => 'Review Loan',
            'code' => 'RL-1',
            'is_active' => true,
        ]);

        $application = Application::create([
            'customer_id' => $customer->id,
            'agent_user_id' => $agent->id,
            'product_id' => $product->id,
            'status' => 'submitted',
        ]);

        $document = Document::create([
            'application_id' => $application->id,
            'document_type' => 'identity_proof',
            'file_path' => 'documents/test.pdf',
            'status' => 'uploaded',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/v1/documents/{$document->id}/review", [
            'status' => 'approved',
            'review_note' => 'Document verified successfully',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'approved')
            ->assertJsonPath('review_note', 'Document verified successfully');

        $this->assertNotNull($document->fresh()->reviewed_by);
        $this->assertNotNull($document->fresh()->reviewed_at);
    }

    public function test_agent_cannot_review_documents(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $customer = Customer::create([
            'agent_user_id' => $agent->id,
            'full_name' => 'No Review Customer',
            'phone' => '9999999999',
            'email' => 'no-review@example.com',
            'status' => 'submitted',
        ]);

        $product = Product::create([
            'name' => 'No Review Loan',
            'code' => 'NRL-1',
            'is_active' => true,
        ]);

        $application = Application::create([
            'customer_id' => $customer->id,
            'agent_user_id' => $agent->id,
            'product_id' => $product->id,
            'status' => 'submitted',
        ]);

        $document = Document::create([
            'application_id' => $application->id,
            'document_type' => 'identity_proof',
            'file_path' => 'documents/test.pdf',
            'status' => 'uploaded',
        ]);

        Sanctum::actingAs($agent);

        $response = $this->postJson("/api/v1/documents/{$document->id}/review", [
            'status' => 'approved',
        ]);

        $response->assertForbidden();
    }

    public function test_duplicate_customer_prevention(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);

        Sanctum::actingAs($agent);

        $this->postJson('/api/v1/customers', [
            'full_name' => 'Duplicate Test',
            'phone' => '1010101010',
            'email' => 'duplicate@example.com',
        ])->assertCreated();

        $response = $this->postJson('/api/v1/customers', [
            'full_name' => 'Duplicate Test Again',
            'phone' => '1010101010',
            'email' => 'another-email@example.com',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('phone');
    }

    public function test_expired_onboarding_token_rejected(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $product = Product::create([
            'name' => 'Expired Token Test',
            'code' => 'ETT-1',
            'is_active' => true,
        ]);

        Sanctum::actingAs($agent);

        $linkResponse = $this->postJson('/api/v1/onboarding-links', [
            'expires_at' => now()->subMinute()->toIso8601String(),
        ]);

        $token = $linkResponse->json('token');

        $response = $this->postJson("/api/v1/onboarding/{$token}/submit", [
            'full_name' => 'Expired Token User',
            'phone' => '1111111110',
            'email' => 'expired-token@example.com',
            'product_id' => $product->id,
        ]);

        $response->assertStatus(410);
    }

    public function test_agent_performance_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent1 = User::factory()->create(['role' => 'agent']);
        $agent2 = User::factory()->create(['role' => 'agent']);

        $product = Product::create([
            'name' => 'Report Test',
            'code' => 'RT-1',
            'is_active' => true,
        ]);

        // Agent1: 2 converted, 1 verified
        for ($i = 0; $i < 2; $i++) {
            $customer = Customer::create([
                'agent_user_id' => $agent1->id,
                'full_name' => "Agent1 Customer{$i}",
                'phone' => "111111111{$i}",
                'email' => "agent1-customer{$i}@example.com",
                'status' => 'converted',
            ]);

            Application::create([
                'customer_id' => $customer->id,
                'agent_user_id' => $agent1->id,
                'product_id' => $product->id,
                'status' => 'converted',
                'converted_at' => now(),
            ]);
        }

        $customer = Customer::create([
            'agent_user_id' => $agent1->id,
            'full_name' => 'Agent1 Verified',
            'phone' => '1111111120',
            'email' => 'agent1-verified@example.com',
            'status' => 'verified',
        ]);

        Application::create([
            'customer_id' => $customer->id,
            'agent_user_id' => $agent1->id,
            'product_id' => $product->id,
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        // Agent2: 1 converted
        $customer = Customer::create([
            'agent_user_id' => $agent2->id,
            'full_name' => 'Agent2 Customer',
            'phone' => '2222222220',
            'email' => 'agent2-customer@example.com',
            'status' => 'converted',
        ]);

        Application::create([
            'customer_id' => $customer->id,
            'agent_user_id' => $agent2->id,
            'product_id' => $product->id,
            'status' => 'converted',
            'converted_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/reports/agent-performance');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(2, $data);

        // Verify Agent1 is first (highest conversion)
        $this->assertEquals($agent1->id, $data[0]['agent_id']);
        $this->assertEquals(2, $data[0]['converted']);
        $this->assertEquals(3, $data[0]['total_applications']);
        $this->assertEquals(66.67, $data[0]['conversion_rate']);
    }

    public function test_conversion_metrics_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'agent']);
        $product = Product::create([
            'name' => 'Metrics Test',
            'code' => 'MT-1',
            'is_active' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $customer = Customer::create([
                'agent_user_id' => $agent->id,
                'full_name' => "Metrics Customer{$i}",
                'phone' => "333333333{$i}",
                'email' => "metrics-customer{$i}@example.com",
                'status' => match ($i % 3) {
                    0 => 'converted',
                    1 => 'verified',
                    default => 'submitted',
                },
            ]);

            Application::create([
                'customer_id' => $customer->id,
                'agent_user_id' => $agent->id,
                'product_id' => $product->id,
                'status' => match ($i % 3) {
                    0 => 'converted',
                    1 => 'verified',
                    default => 'submitted',
                },
            ]);
        }

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/reports/conversion-metrics');

        $response->assertOk()
            ->assertJsonPath('total_applications', 5)
            ->assertJsonPath('converted_count', 2)
            ->assertJsonPath('verified_count', 2)
            ->assertJsonPath('submitted_count', 1);

        $this->assertEquals(40.0, $response->json('conversion_rate'));
    }

    public function test_product_wise_summary_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'agent']);

        $product1 = Product::create([
            'name' => 'Product A',
            'code' => 'PA-1',
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'name' => 'Product B',
            'code' => 'PB-1',
            'is_active' => true,
        ]);

        // Create 3 applications for Product A
        for ($i = 0; $i < 3; $i++) {
            $customer = Customer::create([
                'agent_user_id' => $agent->id,
                'full_name' => "Product A Customer{$i}",
                'phone' => "444444444{$i}",
                'email' => "product-a-customer{$i}@example.com",
                'status' => 'submitted',
            ]);

            Application::create([
                'customer_id' => $customer->id,
                'agent_user_id' => $agent->id,
                'product_id' => $product1->id,
                'status' => 'submitted',
            ]);
        }

        // Create 2 applications for Product B
        for ($i = 0; $i < 2; $i++) {
            $customer = Customer::create([
                'agent_user_id' => $agent->id,
                'full_name' => "Product B Customer{$i}",
                'phone' => "555555555{$i}",
                'email' => "product-b-customer{$i}@example.com",
                'status' => 'submitted',
            ]);

            Application::create([
                'customer_id' => $customer->id,
                'agent_user_id' => $agent->id,
                'product_id' => $product2->id,
                'status' => 'submitted',
            ]);
        }

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/reports/product-wise');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(2, $data);

        $productA = collect($data)->firstWhere('product_name', 'Product A');
        $this->assertEquals(3, $productA['total']);

        $productB = collect($data)->firstWhere('product_name', 'Product B');
        $this->assertEquals(2, $productB['total']);
    }
}

