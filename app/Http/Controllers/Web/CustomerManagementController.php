<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::with('agent', 'products')->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where('full_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->income_type) {
            $query->where('income_type', $request->income_type);
        }

        if ($request->filled('star_rating') && in_array($request->star_rating, ['1','2','3','4','5'])) {
            $query->where('star_rating', $request->star_rating);
        }

        $customers = $query->paginate(15);

        return view('admin.customers.index', [
            'customers' => $customers,
            'statuses' => ['draft', 'submitted', 'verified', 'converted'],
        ]);
    }

    public function create(): View
    {
        $agents   = User::where('role', 'agent')->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.customers.create', compact('agents', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'anniversary_date' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:20', 'unique:customers'],
            'email' => ['required', 'email', 'unique:customers'],
            'pan_number' => ['nullable', 'string', 'max:20', 'unique:customers'],
            'aadhar_number' => ['nullable', 'string', 'max:20', 'unique:customers'],
            'pan_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'aadhar_front_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'aadhar_back_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'passport_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'address' => ['nullable', 'string', 'max:500'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'income_type' => ['nullable', 'in:job,business'],
            'income_details' => ['nullable', 'string', 'max:1000'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relationship' => ['nullable', 'string', 'max:100'],
            'nominee_dob' => ['nullable', 'date', 'before:today'],
            'nominee_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_dob' => ['nullable', 'date'],
            'child1_name' => ['nullable', 'string', 'max:255'],
            'child1_dob' => ['nullable', 'date'],
            'child2_name' => ['nullable', 'string', 'max:255'],
            'child2_dob' => ['nullable', 'date'],
            'child3_name' => ['nullable', 'string', 'max:255'],
            'child3_dob' => ['nullable', 'date'],
            'agent_user_id' => ['nullable', 'exists:users,id'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => ['required_with:documents', 'string', 'max:255'],
            'documents.*.file' => ['required_with:documents', 'file', 'max:5120'],
        ]);

        $products = $validated['products'] ?? [];
        $documents = $validated['documents'] ?? [];
        unset($validated['products'], $validated['documents']);

        foreach (['pan_file', 'aadhar_front_file', 'aadhar_back_file', 'passport_file'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field . '_path'] = $request->file($field)->store('customer_kyc', 'public');
            }
            unset($validated[$field]);
        }

        if ($request->hasFile('nominee_document')) {
            $validated['nominee_document_path'] = $request->file('nominee_document')->store('customer_kyc', 'public');
        }
        unset($validated['nominee_document']);

        $validated['agent_user_id'] = $validated['agent_user_id'] ?? auth()->id();

        $customer = Customer::create([...$validated, 'status' => 'draft']);
        
        if (!empty($products)) {
            $customer->products()->sync($products);
        }

        foreach ($documents as $documentData) {
            $storedPath = $documentData['file']->store('customer_documents', 'public');
            CustomerDocument::create([
                'customer_id' => $customer->id,
                'document_type' => $documentData['document_type'],
                'file_path' => $storedPath,
                'status' => 'uploaded',
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully');
    }

    public function show(Customer $customer): View
    {
        $customer->load('agent', 'products', 'applications');
        
        return view('admin.customers.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer): View
    {
        $agents   = User::where('role', 'agent')->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedProducts = $customer->products()->pluck('products.id')->toArray();
        $customer->load('country', 'state', 'city');

        return view('admin.customers.edit', compact('customer', 'agents', 'products', 'selectedProducts'));
    }

    public function updateRating(Request $request, Customer $customer)
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5']);
        $customer->update(['star_rating' => $request->rating]);
        return response()->json(['success' => true, 'rating' => $customer->star_rating]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'anniversary_date' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:20', "unique:customers,phone,{$customer->id}"],
            'email' => ['required', 'email', "unique:customers,email,{$customer->id}"],
            'pan_number' => ['nullable', 'string', 'max:20', "unique:customers,pan_number,{$customer->id}"],
            'aadhar_number' => ['nullable', 'string', 'max:20', "unique:customers,aadhar_number,{$customer->id}"],
            'pan_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'aadhar_front_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'aadhar_back_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'passport_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'address' => ['nullable', 'string', 'max:500'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'income_type' => ['nullable', 'in:job,business'],
            'income_details' => ['nullable', 'string', 'max:1000'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relationship' => ['nullable', 'string', 'max:100'],
            'nominee_dob' => ['nullable', 'date', 'before:today'],
            'nominee_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_dob' => ['nullable', 'date'],
            'child1_name' => ['nullable', 'string', 'max:255'],
            'child1_dob' => ['nullable', 'date'],
            'child2_name' => ['nullable', 'string', 'max:255'],
            'child2_dob' => ['nullable', 'date'],
            'child3_name' => ['nullable', 'string', 'max:255'],
            'child3_dob' => ['nullable', 'date'],
            'agent_user_id' => ['nullable', 'exists:users,id'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => ['required_with:documents', 'string', 'max:255'],
            'documents.*.file' => ['required_with:documents', 'file', 'max:5120'],
        ]);

        $products = $validated['products'] ?? [];
        $documents = $validated['documents'] ?? [];
        unset($validated['products'], $validated['documents']);

        foreach (['pan_file', 'aadhar_front_file', 'aadhar_back_file', 'passport_file'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field . '_path'] = $request->file($field)->store('customer_kyc', 'public');
            }
            unset($validated[$field]);
        }

        if ($request->hasFile('nominee_document')) {
            $validated['nominee_document_path'] = $request->file('nominee_document')->store('customer_kyc', 'public');
        }
        unset($validated['nominee_document']);

        $validated['agent_user_id'] = $validated['agent_user_id'] ?? auth()->id();

        $customer->update($validated);
        $customer->products()->sync($products);

        foreach ($documents as $documentData) {
            $storedPath = $documentData['file']->store('customer_documents', 'public');
            CustomerDocument::create([
                'customer_id' => $customer->id,
                'document_type' => $documentData['document_type'],
                'file_path' => $storedPath,
                'status' => 'uploaded',
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully');
    }
}
