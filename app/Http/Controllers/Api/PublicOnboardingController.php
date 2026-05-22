<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Customer;
use App\Models\Document;
use App\Models\OnboardingLink;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicOnboardingController extends Controller
{
    public function show(string $token)
    {
        $link = OnboardingLink::query()
            ->where('token', $token)
            ->first();

        if (! $link) {
            return response()->json(['message' => 'Invalid onboarding token.'], 404);
        }

        if ($link->expires_at && $link->expires_at->isPast()) {
            return response()->json(['message' => 'Onboarding token has expired.'], 410);
        }

        return response()->json([
            'token' => $link->token,
            'customer_id' => $link->customer_id,
            'expires_at' => $link->expires_at,
            'used_at' => $link->used_at,
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $link = OnboardingLink::query()
            ->where('token', $token)
            ->first();

        if (! $link) {
            return response()->json(['message' => 'Invalid onboarding token.'], 404);
        }

        if ($link->expires_at && $link->expires_at->isPast()) {
            return response()->json(['message' => 'Onboarding token has expired.'], 410);
        }

        if ($link->used_at) {
            return response()->json(['message' => 'Onboarding token has already been used.'], 409);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'profile_payload' => ['nullable', 'array'],
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => ['required_with:documents', 'string', 'max:255'],
            'documents.*.file' => ['required_with:documents', 'file', 'max:5120'],
        ]);

        $customer = $link->customer_id
            ? Customer::findOrFail($link->customer_id)
            : Customer::create([
                'agent_user_id' => $link->agent_user_id,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

        if (! $link->customer_id) {
            $link->update(['customer_id' => $customer->id]);
        } else {
            $customer->update([
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        }

        $application = Application::create([
            'customer_id' => $customer->id,
            'agent_user_id' => $link->agent_user_id,
            'product_id' => Product::findOrFail($validated['product_id'])->id,
            'status' => 'submitted',
            'profile_payload' => $validated['profile_payload'] ?? null,
            'submitted_at' => now(),
        ]);

        $createdDocuments = [];
        foreach ($validated['documents'] ?? [] as $documentInput) {
            $storedPath = $documentInput['file']->store('documents', 'public');
            $createdDocuments[] = Document::create([
                'application_id' => $application->id,
                'document_type' => $documentInput['document_type'],
                'file_path' => $storedPath,
                'status' => 'uploaded',
            ]);
        }

        $link->update([
            'used_at' => now(),
        ]);

        return response()->json([
            'customer' => $customer,
            'application' => $application,
            'documents' => $createdDocuments,
        ], 201);
    }
}
