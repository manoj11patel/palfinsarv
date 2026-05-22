<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        // Get customer record if it exists
        $customer = \App\Models\Customer::where('email', $user->email)->first();
        
        $applications = [];
        $totalApplications = 0;
        $submittedApplications = 0;
        $verifiedApplications = 0;
        $convertedApplications = 0;

        if ($customer) {
            $applications = Application::with(['product'])
                ->where('customer_id', $customer->id)
                ->latest()
                ->get();

            $totalApplications = $applications->count();
            $submittedApplications = $applications->where('status', 'submitted')->count();
            $verifiedApplications = $applications->where('status', 'verified')->count();
            $convertedApplications = $applications->where('status', 'converted')->count();
        }

        return view('customer.dashboard', [
            'customer' => $customer,
            'applications' => $applications,
            'totalApplications' => $totalApplications,
            'submittedApplications' => $submittedApplications,
            'verifiedApplications' => $verifiedApplications,
            'convertedApplications' => $convertedApplications,
        ]);
    }
}
