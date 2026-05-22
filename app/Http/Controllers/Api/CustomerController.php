<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()->with('agent', 'state', 'products')->latest();

        if ($request->user()->role !== 'admin') {
            $query->where('agent_user_id', $request->user()->id);
        }

        if ($request->filled('income_type') && in_array($request->income_type, ['job', 'business'])) {
            $query->where('income_type', $request->income_type);
        }

        $customers = $query->paginate(20);

        return response()->json($customers);
    }

    public function show(Request $request, Customer $customer)
    {
        // Agents can only view their own customers
        if ($request->user()->role !== 'admin' && $customer->agent_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $customer->load('agent', 'state', 'products', 'applications');

        return response()->json([
            'id'                   => $customer->id,
            'full_name'            => $customer->full_name,
            'email'                => $customer->email,
            'phone'                => $customer->phone,
            'date_of_birth'        => $customer->date_of_birth?->format('Y-m-d'),
            'anniversary_date'     => $customer->anniversary_date?->format('Y-m-d'),
            'status'               => $customer->status,
            'address'              => $customer->address,
            'city'                 => $customer->city,
            'state'                => $customer->state ? ['id' => $customer->state->id, 'name' => $customer->state->name] : null,
            'pincode'              => $customer->pincode,
            'pan_number'           => $customer->pan_number,
            'account_holder_name'  => $customer->account_holder_name,
            'bank_name'            => $customer->bank_name,
            'account_number'       => $customer->account_number,
            'ifsc_code'            => $customer->ifsc_code,
            'fund_name'            => $customer->fund_name,
            'investment_type'      => $customer->investment_type,
            'investment_amount'    => $customer->investment_amount,
            'income_type'          => $customer->income_type,
            'income_details'       => $customer->income_details,
            'nominee_name'         => $customer->nominee_name,
            'nominee_relationship' => $customer->nominee_relationship,
            'spouse_name'          => $customer->spouse_name,
            'spouse_dob'           => $customer->spouse_dob?->format('Y-m-d'),
            'child1_name'          => $customer->child1_name,
            'child1_dob'           => $customer->child1_dob?->format('Y-m-d'),
            'child2_name'          => $customer->child2_name,
            'child2_dob'           => $customer->child2_dob?->format('Y-m-d'),
            'child3_name'          => $customer->child3_name,
            'child3_dob'           => $customer->child3_dob?->format('Y-m-d'),
            'agent'                => $customer->agent ? ['id' => $customer->agent->id, 'name' => $customer->agent->name] : null,
            'products'             => $customer->products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name]),
            'applications'         => $customer->applications->map(fn ($a) => [
                'id'           => $a->id,
                'product_id'   => $a->product_id,
                'status'       => $a->status,
                'submitted_at' => $a->submitted_at?->toISOString(),
                'verified_at'  => $a->verified_at?->toISOString(),
                'converted_at' => $a->converted_at?->toISOString(),
            ]),
            'created_at'           => $customer->created_at->toISOString(),
            'updated_at'           => $customer->updated_at->toISOString(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'            => ['required', 'string', 'max:255'],
            'date_of_birth'        => ['nullable', 'date'],
            'phone'                => ['required', 'string', 'max:20', 'unique:customers,phone'],
            'email'                => ['required', 'email', 'max:255', 'unique:customers,email'],
            'pan_number'           => ['nullable', 'string', 'max:20', 'unique:customers,pan_number'],
            'address'              => ['nullable', 'string', 'max:500'],
            'city'                 => ['nullable', 'string', 'max:100'],
            'state_id'             => ['nullable', 'exists:states,id'],
            'pincode'              => ['nullable', 'string', 'max:20'],
            'account_holder_name'  => ['nullable', 'string', 'max:255'],
            'bank_name'            => ['nullable', 'string', 'max:255'],
            'account_number'       => ['nullable', 'string', 'max:50'],
            'ifsc_code'            => ['nullable', 'string', 'max:20'],
            'fund_name'            => ['nullable', 'string', 'max:255'],
            'investment_type'      => ['nullable', 'in:SIP,Lump Sum'],
            'investment_amount'    => ['nullable', 'numeric', 'min:0'],
            'income_type'          => ['nullable', 'in:job,business'],
            'income_details'       => ['nullable', 'string', 'max:1000'],
            'nominee_name'         => ['nullable', 'string', 'max:255'],
            'nominee_relationship' => ['nullable', 'string', 'max:100'],
            'products'             => ['nullable', 'array'],
            'products.*'           => ['exists:products,id'],
        ]);

        $products = $validated['products'] ?? [];
        unset($validated['products']);

        $customer = Customer::create([
            ...$validated,
            'agent_user_id' => auth()->id(),
            'status'        => 'draft',
        ]);

        if (!empty($products)) {
            $customer->products()->sync($products);
        }

        $customer->load('agent', 'state', 'products');

        return response()->json($customer, 201);
    }
}
