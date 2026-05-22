<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Application::with(['customer', 'product', 'agent'])->latest();

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $applications = $query->paginate(15);
        $products     = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.applications.index', [
            'applications' => $applications,
            'products'     => $products,
            'statuses'     => ['draft', 'submitted', 'verified', 'converted'],
        ]);
    }

    public function create(): View
    {
        $customers = Customer::orderBy('full_name')->get();
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $agents    = User::where('role', 'agent')->orderBy('name')->get();

        return view('admin.applications.create', compact('customers', 'products', 'agents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'product_id'    => 'required|exists:products,id',
            'agent_user_id' => 'nullable|exists:users,id',
            'status'        => 'required|in:draft,submitted',
        ]);

        // Prevent duplicate active application for same customer + product
        $exists = Application::where('customer_id', $request->customer_id)
            ->where('product_id', $request->product_id)
            ->whereNotIn('status', ['converted'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'An active application already exists for this customer and product.');
        }

        $application = Application::create([
            'customer_id'   => $request->customer_id,
            'product_id'    => $request->product_id,
            'agent_user_id' => $request->agent_user_id ?? auth()->id(),
            'status'        => $request->status,
            'submitted_at'  => $request->status === 'submitted' ? now() : null,
        ]);

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Application created successfully.');
    }

    public function show(Application $application): View
    {
        $application->load(['customer', 'product', 'documents', 'agent']);

        return view('admin.applications.show', ['application' => $application]);
    }

    public function verify(Request $request, Application $application): RedirectResponse
    {
        if ($application->status !== 'submitted') {
            return back()->with('error', 'Only submitted applications can be verified');
        }

        $application->update([
            'status'      => 'verified',
            'verified_at' => now(),
        ]);

        $application->customer()->update(['status' => 'verified']);

        return back()->with('success', 'Application verified successfully');
    }

    public function convert(Request $request, Application $application): RedirectResponse
    {
        if ($application->status !== 'verified') {
            return back()->with('error', 'Only verified applications can be converted');
        }

        $application->update([
            'status'       => 'converted',
            'converted_at' => now(),
        ]);

        $application->customer()->update(['status' => 'converted']);

        return back()->with('success', 'Application converted successfully');
    }
}
