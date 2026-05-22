<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function agentSummary(Request $request)
    {
        $summary = Application::query()
            ->select('status', DB::raw('count(*) as total'))
            ->where('agent_user_id', $request->user()->id)
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'agent_id' => $request->user()->id,
            'summary' => $summary,
        ]);
    }

    public function productWiseSummary(Request $request)
    {
        $query = Application::query()
            ->join('products', 'applications.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'applications.status',
                DB::raw('count(*) as total')
            )
            ->groupBy('products.id', 'products.name', 'applications.status');

        if ($request->user()->role !== 'admin') {
            $query->where('applications.agent_user_id', $request->user()->id);
        }

        $summary = $query->orderBy('products.name')->get();

        return response()->json([
            'data' => $summary,
        ]);
    }

    public function conversionMetrics(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = Application::query();

        if ($request->user()->role !== 'admin') {
            $query->where('agent_user_id', $request->user()->id);
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $total = $query->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $verified = (clone $query)->where('status', 'verified')->count();
        $converted = (clone $query)->where('status', 'converted')->count();

        return response()->json([
            'total_applications' => $total,
            'submitted_count' => $submitted,
            'verified_count' => $verified,
            'converted_count' => $converted,
            'conversion_rate' => $total > 0 ? round(($converted / $total) * 100, 2) : 0,
            'submission_rate' => $total > 0 ? round(($submitted / $total) * 100, 2) : 0,
        ]);
    }

    public function agentPerformance(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = Application::query()
            ->select(
                'users.id as agent_id',
                'users.name as agent_name',
                DB::raw('count(*) as total_applications'),
                DB::raw("sum(case when applications.status = 'converted' then 1 else 0 end) as converted"),
                DB::raw("sum(case when applications.status = 'verified' then 1 else 0 end) as verified")
            )
            ->join('users', 'applications.agent_user_id', '=', 'users.id')
            ->where('users.role', 'agent');

        if ($request->start_date) {
            $query->whereDate('applications.created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('applications.created_at', '<=', $request->end_date);
        }

        $results = $query
            ->groupBy('users.id', 'users.name')
            ->orderBy('converted', 'desc')
            ->get()
            ->map(function ($agent) {
                return [
                    'agent_id' => $agent->agent_id,
                    'agent_name' => $agent->agent_name,
                    'total_applications' => $agent->total_applications,
                    'converted' => $agent->converted,
                    'verified' => $agent->verified,
                    'conversion_rate' => $agent->total_applications > 0
                        ? round(($agent->converted / $agent->total_applications) * 100, 2)
                        : 0,
                ];
            });

        return response()->json([
            'data' => $results,
        ]);
    }
}
