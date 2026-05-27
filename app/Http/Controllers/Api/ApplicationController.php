<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Customer;
use App\Traits\AuditLoggingTrait;
use Illuminate\Http\Request;



class ApplicationController extends Controller
{
    use AuditLoggingTrait;

    public function index(Request $request)
    {
        $query = Application::query()->with(['customer', 'product'])->latest();

        if ($request->user()->role !== 'admin') {
            $query->where('agent_user_id', $request->user()->id);
        }

        $applications = $query->paginate(20);

        return response()->json($applications);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'profile_payload' => ['nullable', 'array'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        if ($request->user()->role !== 'admin' && $customer->agent_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $application = Application::create([
            ...$validated,
            'agent_user_id' => $request->user()->role === 'admin' ? $customer->agent_user_id : $request->user()->id,
            'status' => 'draft',
        ]);

        self::logAudit('created', 'Application', $application->id, [
            'customer_id' => $customer->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json($application, 201);
    }

    public function submit(Request $request, Application $application)
    {
        if ($request->user()->role !== 'admin' && $application->agent_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($application->status !== 'draft') {
            return response()->json(['message' => 'Only draft applications can be submitted.'], 422);
        }

        $application->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        self::logAudit('submitted', 'Application', $application->id, [
            'previous_status' => 'draft',
            'new_status' => 'submitted',
        ]);

        return response()->json($application);
    }

    public function verify(Request $request, Application $application)
    {
        if ($application->status !== 'submitted') {
            return response()->json(['message' => 'Only submitted applications can be verified.'], 422);
        }

        $application->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $application->customer()->update([
            'status' => 'verified',
        ]);

        self::logAudit('verified', 'Application', $application->id, [
            'previous_status' => 'submitted',
            'new_status' => 'verified',
        ]);

        return response()->json($application);
    }

    public function convert(Request $request, Application $application)
    {
        if ($application->status !== 'verified') {
            return response()->json(['message' => 'Only verified applications can be converted.'], 422);
        }

        $application->update([
            'status' => 'converted',
            'converted_at' => now(),
        ]);

        $application->customer()->update([
            'status' => 'converted',
        ]);

        self::logAudit('converted', 'Application', $application->id, [
            'previous_status' => 'verified',
            'new_status' => 'converted',
        ]);

        return response()->json($application);
    }
}
