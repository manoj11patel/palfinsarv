<?php

namespace Database\Seeders;

use App\Models\AgentProfile;
use App\Models\Application;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Geographic data is provided by nnjeim/world.
        // Run once separately: php artisan db:seed --class=WorldSeeder

        // Create Admin Users
        $admin1 = User::updateOrCreate(
            ['email' => 'admin@digital-system.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $admin2 = User::updateOrCreate(
            ['email' => 'admin2@digital-system.test'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Agent Users
        $agent1 = User::updateOrCreate(
            ['email' => 'agent1@digital-system.test'],
            [
                'name' => 'Agent User',
                'password' => Hash::make('password'),
                'role' => 'agent',
            ]
        );

        $agent2 = User::updateOrCreate(
            ['email' => 'agent2@digital-system.test'],
            [
                'name' => 'Rajesh Kumar',
                'password' => Hash::make('password'),
                'role' => 'agent',
            ]
        );

        $agent3 = User::updateOrCreate(
            ['email' => 'agent3@digital-system.test'],
            [
                'name' => 'Priya Singh',
                'password' => Hash::make('password'),
                'role' => 'agent',
            ]
        );

        // Create Customer Users
        $customer1 = User::updateOrCreate(
            ['email' => 'customer@digital-system.test'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // Create Agent Profiles
        AgentProfile::updateOrCreate(
            ['user_id' => $agent1->id],
            [
                'employee_code' => 'AGENT-001',
                'phone' => '9999999999',
                'is_active' => true,
            ]
        );

        AgentProfile::updateOrCreate(
            ['user_id' => $agent2->id],
            [
                'employee_code' => 'AGENT-002',
                'phone' => '9999999998',
                'is_active' => true,
            ]
        );

        AgentProfile::updateOrCreate(
            ['user_id' => $agent3->id],
            [
                'employee_code' => 'AGENT-003',
                'phone' => '9999999997',
                'is_active' => true,
            ]
        );

        // Create Products
        $personalLoan = Product::updateOrCreate(
            ['code' => 'PERSONAL-LOAN'],
            [
                'name' => 'Personal Loan',
                'description' => 'Standard personal loan product for testing.',
                'is_active' => true,
            ]
        );

        $homeLoan = Product::updateOrCreate(
            ['code' => 'HOME-LOAN'],
            [
                'name' => 'Home Loan',
                'description' => 'Home loan product for real estate purchases.',
                'is_active' => true,
            ]
        );

        $autoLoan = Product::updateOrCreate(
            ['code' => 'AUTO-LOAN'],
            [
                'name' => 'Auto Loan',
                'description' => 'Vehicle financing product.',
                'is_active' => true,
            ]
        );

        // ============ DRAFT APPLICATIONS ============
        
        // Customer 1 - Draft Application
        $customer1_draft = Customer::updateOrCreate(
            ['email' => 'john.draft@example.com'],
            [
                'full_name' => 'John Draft',
                'phone' => '9876543210',
                'agent_user_id' => $agent1->id,
                'status' => 'draft',
            ]
        );

        Application::updateOrCreate(
            ['id' => 1],
            [
                'customer_id' => $customer1_draft->id,
                'agent_user_id' => $agent1->id,
                'product_id' => $personalLoan->id,
                'status' => 'draft',
                'profile_payload' => json_encode([
                    'full_name' => 'John Draft',
                    'email' => 'john.draft@example.com',
                    'phone' => '9876543210',
                    'income' => 500000,
                    'loan_amount' => 100000,
                ]),
            ]
        );

        // ============ SUBMITTED APPLICATIONS ============
        
        // Customer 2 - Submitted Application (Waiting for verification)
        $customer2_submitted = Customer::updateOrCreate(
            ['email' => 'sarah.submitted@example.com'],
            [
                'full_name' => 'Sarah Johnson',
                'phone' => '9876543211',
                'agent_user_id' => $agent1->id,
                'status' => 'submitted',
            ]
        );

        $app2 = Application::updateOrCreate(
            ['id' => 2],
            [
                'customer_id' => $customer2_submitted->id,
                'agent_user_id' => $agent1->id,
                'product_id' => $personalLoan->id,
                'status' => 'submitted',
                'submitted_at' => now()->subDays(3),
                'profile_payload' => json_encode([
                    'full_name' => 'Sarah Johnson',
                    'email' => 'sarah.submitted@example.com',
                    'phone' => '9876543211',
                    'income' => 750000,
                    'loan_amount' => 250000,
                    'employment_type' => 'Salaried',
                    'company_name' => 'Tech Corp',
                ]),
            ]
        );

        // Add documents to submitted application
        Document::updateOrCreate(
            ['id' => 1],
            [
                'application_id' => $app2->id,
                'document_type' => 'ID_PROOF',
                'file_path' => 'storage/documents/id_proof_sarah.pdf',
                'status' => 'uploaded',
            ]
        );

        Document::updateOrCreate(
            ['id' => 2],
            [
                'application_id' => $app2->id,
                'document_type' => 'SALARY_SLIP',
                'file_path' => 'storage/documents/salary_slip_sarah.pdf',
                'status' => 'uploaded',
            ]
        );

        // Customer 3 - Another Submitted Application
        $customer3_submitted = Customer::updateOrCreate(
            ['email' => 'mike.submitted@example.com'],
            [
                'full_name' => 'Mike Davis',
                'phone' => '9876543212',
                'agent_user_id' => $agent2->id,
                'status' => 'submitted',
            ]
        );

        $app3 = Application::updateOrCreate(
            ['id' => 3],
            [
                'customer_id' => $customer3_submitted->id,
                'agent_user_id' => $agent2->id,
                'product_id' => $homeLoan->id,
                'status' => 'submitted',
                'submitted_at' => now()->subDays(5),
                'profile_payload' => json_encode([
                    'full_name' => 'Mike Davis',
                    'email' => 'mike.submitted@example.com',
                    'phone' => '9876543212',
                    'income' => 1200000,
                    'loan_amount' => 2500000,
                    'property_value' => 3000000,
                ]),
            ]
        );

        Document::updateOrCreate(
            ['id' => 3],
            [
                'application_id' => $app3->id,
                'document_type' => 'PROPERTY_DOCUMENT',
                'file_path' => 'storage/documents/property_doc_mike.pdf',
                'status' => 'uploaded',
            ]
        );

        // Customer 4 - Submitted with pending review document
        $customer4_submitted = Customer::updateOrCreate(
            ['email' => 'emily.submitted@example.com'],
            [
                'full_name' => 'Emily Wilson',
                'phone' => '9876543213',
                'agent_user_id' => $agent3->id,
                'status' => 'submitted',
            ]
        );

        $app4 = Application::updateOrCreate(
            ['id' => 4],
            [
                'customer_id' => $customer4_submitted->id,
                'agent_user_id' => $agent3->id,
                'product_id' => $autoLoan->id,
                'status' => 'submitted',
                'submitted_at' => now()->subDays(2),
                'profile_payload' => json_encode([
                    'full_name' => 'Emily Wilson',
                    'email' => 'emily.submitted@example.com',
                    'phone' => '9876543213',
                    'income' => 550000,
                    'loan_amount' => 800000,
                ]),
            ]
        );

        Document::updateOrCreate(
            ['id' => 4],
            [
                'application_id' => $app4->id,
                'document_type' => 'ID_PROOF',
                'file_path' => 'storage/documents/id_proof_emily.pdf',
                'status' => 'pending review',
            ]
        );

        // ============ VERIFIED APPLICATIONS ============
        
        // Customer 5 - Verified Application (Ready for conversion)
        $customer5_verified = Customer::updateOrCreate(
            ['email' => 'james.verified@example.com'],
            [
                'full_name' => 'James Brown',
                'phone' => '9876543214',
                'agent_user_id' => $agent1->id,
                'status' => 'verified',
            ]
        );

        $app5 = Application::updateOrCreate(
            ['id' => 5],
            [
                'customer_id' => $customer5_verified->id,
                'agent_user_id' => $agent1->id,
                'product_id' => $personalLoan->id,
                'status' => 'verified',
                'submitted_at' => now()->subDays(10),
                'verified_at' => now()->subDays(5),
                'profile_payload' => json_encode([
                    'full_name' => 'James Brown',
                    'email' => 'james.verified@example.com',
                    'phone' => '9876543214',
                    'income' => 650000,
                    'loan_amount' => 180000,
                ]),
            ]
        );

        Document::updateOrCreate(
            ['id' => 5],
            [
                'application_id' => $app5->id,
                'document_type' => 'ID_PROOF',
                'file_path' => 'storage/documents/id_proof_james.pdf',
                'status' => 'approved',
                'reviewed_by' => $admin1->id,
                'reviewed_at' => now()->subDays(5),
            ]
        );

        Document::updateOrCreate(
            ['id' => 6],
            [
                'application_id' => $app5->id,
                'document_type' => 'SALARY_SLIP',
                'file_path' => 'storage/documents/salary_slip_james.pdf',
                'status' => 'approved',
                'reviewed_by' => $admin1->id,
                'reviewed_at' => now()->subDays(5),
            ]
        );

        // Customer 6 - Another Verified Application
        $customer6_verified = Customer::updateOrCreate(
            ['email' => 'sofia.verified@example.com'],
            [
                'full_name' => 'Sofia Martinez',
                'phone' => '9876543215',
                'agent_user_id' => $agent2->id,
                'status' => 'verified',
            ]
        );

        $app6 = Application::updateOrCreate(
            ['id' => 6],
            [
                'customer_id' => $customer6_verified->id,
                'agent_user_id' => $agent2->id,
                'product_id' => $homeLoan->id,
                'status' => 'verified',
                'submitted_at' => now()->subDays(8),
                'verified_at' => now()->subDays(3),
                'profile_payload' => json_encode([
                    'full_name' => 'Sofia Martinez',
                    'email' => 'sofia.verified@example.com',
                    'phone' => '9876543215',
                    'income' => 1500000,
                    'loan_amount' => 3000000,
                ]),
            ]
        );

        // ============ CONVERTED APPLICATIONS (SUCCESS) ============
        
        // Customer 7 - Successfully Converted
        $customer7_converted = Customer::updateOrCreate(
            ['email' => 'alex.converted@example.com'],
            [
                'full_name' => 'Alex Thompson',
                'phone' => '9876543216',
                'agent_user_id' => $agent1->id,
                'status' => 'converted',
            ]
        );

        Application::updateOrCreate(
            ['id' => 7],
            [
                'customer_id' => $customer7_converted->id,
                'agent_user_id' => $agent1->id,
                'product_id' => $personalLoan->id,
                'status' => 'converted',
                'submitted_at' => now()->subDays(15),
                'verified_at' => now()->subDays(10),
                'converted_at' => now()->subDays(2),
                'profile_payload' => json_encode([
                    'full_name' => 'Alex Thompson',
                    'email' => 'alex.converted@example.com',
                    'phone' => '9876543216',
                    'income' => 900000,
                    'loan_amount' => 350000,
                ]),
            ]
        );

        // Customer 8 - Another Converted
        $customer8_converted = Customer::updateOrCreate(
            ['email' => 'lucy.converted@example.com'],
            [
                'full_name' => 'Lucy Chen',
                'phone' => '9876543217',
                'agent_user_id' => $agent2->id,
                'status' => 'converted',
            ]
        );

        Application::updateOrCreate(
            ['id' => 8],
            [
                'customer_id' => $customer8_converted->id,
                'agent_user_id' => $agent2->id,
                'product_id' => $autoLoan->id,
                'status' => 'converted',
                'submitted_at' => now()->subDays(20),
                'verified_at' => now()->subDays(15),
                'converted_at' => now()->subDays(5),
                'profile_payload' => json_encode([
                    'full_name' => 'Lucy Chen',
                    'email' => 'lucy.converted@example.com',
                    'phone' => '9876543217',
                    'income' => 720000,
                    'loan_amount' => 600000,
                ]),
            ]
        );

        // Customer 9 - Converted Home Loan
        $customer9_converted = Customer::updateOrCreate(
            ['email' => 'robert.converted@example.com'],
            [
                'full_name' => 'Robert Anderson',
                'phone' => '9876543218',
                'agent_user_id' => $agent3->id,
                'status' => 'converted',
            ]
        );

        Application::updateOrCreate(
            ['id' => 9],
            [
                'customer_id' => $customer9_converted->id,
                'agent_user_id' => $agent3->id,
                'product_id' => $homeLoan->id,
                'status' => 'converted',
                'submitted_at' => now()->subDays(25),
                'verified_at' => now()->subDays(20),
                'converted_at' => now()->subDays(8),
                'profile_payload' => json_encode([
                    'full_name' => 'Robert Anderson',
                    'email' => 'robert.converted@example.com',
                    'phone' => '9876543218',
                    'income' => 2000000,
                    'loan_amount' => 4500000,
                ]),
            ]
        );

        // Customer 10 - Converted Personal Loan
        $customer10_converted = Customer::updateOrCreate(
            ['email' => 'diana.converted@example.com'],
            [
                'full_name' => 'Diana Foster',
                'phone' => '9876543219',
                'agent_user_id' => $agent1->id,
                'status' => 'converted',
            ]
        );

        Application::updateOrCreate(
            ['id' => 10],
            [
                'customer_id' => $customer10_converted->id,
                'agent_user_id' => $agent1->id,
                'product_id' => $personalLoan->id,
                'status' => 'converted',
                'submitted_at' => now()->subDays(12),
                'verified_at' => now()->subDays(8),
                'converted_at' => now()->subDays(1),
                'profile_payload' => json_encode([
                    'full_name' => 'Diana Foster',
                    'email' => 'diana.converted@example.com',
                    'phone' => '9876543219',
                    'income' => 850000,
                    'loan_amount' => 200000,
                ]),
            ]
        );
    }
}
