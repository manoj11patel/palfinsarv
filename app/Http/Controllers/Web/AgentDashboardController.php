<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\View\View;

class AgentDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        $totalApplications = Application::where('agent_user_id', $user->id)->count();
        $submittedApplications = Application::where('agent_user_id', $user->id)
            ->where('status', 'submitted')
            ->count();
        $verifiedApplications = Application::where('agent_user_id', $user->id)
            ->where('status', 'verified')
            ->count();
        $convertedApplications = Application::where('agent_user_id', $user->id)
            ->where('status', 'converted')
            ->count();

        $conversionRate = $totalApplications > 0 
            ? round(($convertedApplications / $totalApplications) * 100, 2) 
            : 0;

        $recentApplications = Application::with(['customer', 'product'])
            ->where('agent_user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('agent.dashboard', [
            'totalApplications' => $totalApplications,
            'submittedApplications' => $submittedApplications,
            'verifiedApplications' => $verifiedApplications,
            'convertedApplications' => $convertedApplications,
            'conversionRate' => $conversionRate,
            'recentApplications' => $recentApplications,
        ]);
    }
}
