<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OnboardingLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingLinkController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if (isset($validated['customer_id'])) {
            $customer = Customer::findOrFail($validated['customer_id']);
            if ($request->user()->role !== 'admin' && $customer->agent_user_id !== $request->user()->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $agentUserId = $request->user()->id;
        if (isset($customer)) {
            $agentUserId = $customer->agent_user_id;
        }

        $link = OnboardingLink::create([
            'agent_user_id' => $agentUserId,
            'customer_id' => $validated['customer_id'] ?? null,
            'token' => Str::uuid()->toString(),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return response()->json($link, 201);
    }
}
