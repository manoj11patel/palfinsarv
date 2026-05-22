<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use App\Models\Application;
use App\Models\Customer;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // --- Today's Birthdays ---
        $todayBirthdays = Customer::whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', now()->month)
            ->whereDay('date_of_birth', now()->day)
            ->with('agent')
            ->get();

        $todayAgentBirthdays = AgentProfile::whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', now()->month)
            ->whereDay('date_of_birth', now()->day)
            ->with('user')
            ->get();

        // --- Existing metrics ---
        $totalApplications     = Application::count();
        $convertedApplications = Application::where('status', 'converted')->count();
        $submittedApplications = Application::where('status', 'submitted')->count();
        $totalCustomers        = Customer::count();
        $totalAgents           = User::where('role', 'agent')->count();
        $conversionRate        = $totalApplications > 0
            ? round(($convertedApplications / $totalApplications) * 100, 2)
            : 0;

        $recentApplications = Application::with(['customer', 'product', 'agent'])
            ->latest()
            ->limit(5)
            ->get();

        $applicationsByStatus = Application::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // --- User Status Overview ---

        // 1. Customers who downloaded the app and logged in (have a linked user account)
        $appLoginCount   = Customer::whereNotNull('user_id')->count();
        $recentAppLogins = Customer::with('agent')
            ->whereNotNull('user_id')
            ->latest()
            ->limit(5)
            ->get();

        // 2. Customers with a draft application but NO documents uploaded yet
        $pendingCount   = Customer::whereHas('applications', function ($q) {
            $q->where('status', 'draft')->doesntHave('documents');
        })->count();
        $recentPending  = Customer::with(['agent', 'applications' => function ($q) {
            $q->where('status', 'draft')->doesntHave('documents')->latest()->limit(1);
        }])
            ->whereHas('applications', function ($q) {
                $q->where('status', 'draft')->doesntHave('documents');
            })
            ->latest()
            ->limit(5)
            ->get();

        // 3. Customers whose applications are fully completed (converted)
        $completedCount   = Customer::whereHas('applications', function ($q) {
            $q->where('status', 'converted');
        })->count();
        $recentCompleted  = Customer::with(['agent', 'applications' => function ($q) {
            $q->where('status', 'converted')->latest()->limit(1);
        }])
            ->whereHas('applications', function ($q) {
                $q->where('status', 'converted');
            })
            ->latest()
            ->limit(5)
            ->get();

        // --- Agent Business Leaderboard ---
        $agentLeaderboard = User::where('role', 'agent')
            ->with('agentProfile')
            ->withCount([
                'assignedCustomers as total_customers',
                'assignedCustomers as converted_count' => fn ($q) =>
                    $q->whereHas('applications', fn ($q2) => $q2->where('status', 'converted')),
                'assignedCustomers as pending_count' => fn ($q) =>
                    $q->whereHas('applications', fn ($q2) => $q2->whereIn('status', ['draft', 'submitted'])),
            ])
            ->withSum('assignedCustomers', 'investment_amount')
            ->orderByDesc('total_customers')
            ->get();

        // Grand totals for percentage bars
        $leaderboardTotalCustomers = $agentLeaderboard->sum('total_customers') ?: 1;
        $leaderboardTotalAmount    = $agentLeaderboard->sum('assigned_customers_sum_investment_amount') ?: 1;

        return view('admin.dashboard', [
            'todayBirthdays'        => $todayBirthdays,
            'todayAgentBirthdays'   => $todayAgentBirthdays,
            'totalApplications'     => $totalApplications,
            'convertedApplications' => $convertedApplications,
            'submittedApplications' => $submittedApplications,
            'totalCustomers'        => $totalCustomers,
            'totalAgents'           => $totalAgents,
            'conversionRate'        => $conversionRate,
            'recentApplications'    => $recentApplications,
            'applicationsByStatus'  => $applicationsByStatus,
            // user status
            'appLoginCount'              => $appLoginCount,
            'recentAppLogins'       => $recentAppLogins,
            'pendingCount'          => $pendingCount,
            'recentPending'         => $recentPending,
            'completedCount'             => $completedCount,
            'recentCompleted'            => $recentCompleted,
            // agent leaderboard
            'agentLeaderboard'           => $agentLeaderboard,
            'leaderboardTotalCustomers'  => $leaderboardTotalCustomers,
            'leaderboardTotalAmount'     => $leaderboardTotalAmount,
        ]);
    }
}
