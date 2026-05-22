<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->subMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now();

        $conversionData = Application::selectRaw('status, count(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->pluck('count', 'status');

        $productReport = Application::selectRaw('products.name, applications.status, count(*) as count')
            ->join('products', 'applications.product_id', '=', 'products.id')
            ->whereBetween('applications.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'applications.status')
            ->get()
            ->groupBy('name');

        $agentReport = Application::selectRaw('users.name as agent_name, count(*) as total, sum(case when applications.status = \'converted\' then 1 else 0 end) as converted')
            ->join('users', 'applications.agent_user_id', '=', 'users.id')
            ->whereBetween('applications.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('converted DESC')
            ->get();

        return view('admin.reports.index', [
            'conversionData' => $conversionData,
            'productReport' => $productReport,
            'agentReport' => $agentReport,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ]);
    }
}
