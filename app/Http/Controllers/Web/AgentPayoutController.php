<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use App\Models\AgentPayout;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AgentPayoutController extends Controller
{
    public function store(Request $request, $agentId)
    {
        $request->validate([
            'month'          => 'required|string',
            'year'           => 'required|integer|min:2000|max:2100',
            'total_policies' => 'nullable|integer|min:0',
            'total_amount'   => 'required|numeric|min:0',
            'commission'     => 'required|numeric|min:0',
            'deductions'     => 'nullable|numeric|min:0',
        ]);

        $exists = AgentPayout::where('agent_id', $agentId)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Payout for this month/year already exists.'], 409);
        }

        $deductions  = $request->deductions ?? 0;
        $net_amount  = $request->total_amount + $request->commission - $deductions;

        $payout = AgentPayout::create([
            'agent_id'       => $agentId,
            'month'          => $request->month,
            'year'           => $request->year,
            'total_policies' => $request->total_policies ?? 0,
            'total_amount'   => $request->total_amount,
            'commission'     => $request->commission,
            'deductions'     => $deductions,
            'net_amount'     => $net_amount,
        ]);

        return response()->json(['success' => true, 'payout' => $payout]);
    }

    public function update(Request $request, $payoutId)
    {
        $payout = AgentPayout::findOrFail($payoutId);

        $request->validate([
            'total_policies' => 'nullable|integer|min:0',
            'total_amount'   => 'required|numeric|min:0',
            'commission'     => 'required|numeric|min:0',
            'deductions'     => 'nullable|numeric|min:0',
        ]);

        $deductions = $request->deductions ?? 0;
        $net_amount = $request->total_amount + $request->commission - $deductions;

        $payout->update([
            'total_policies' => $request->total_policies ?? $payout->total_policies,
            'total_amount'   => $request->total_amount,
            'commission'     => $request->commission,
            'deductions'     => $deductions,
            'net_amount'     => $net_amount,
        ]);

        return response()->json(['success' => true, 'payout' => $payout]);
    }

    public function destroy($payoutId)
    {
        $payout = AgentPayout::findOrFail($payoutId);
        $payout->delete();
        return response()->json(['success' => true]);
    }

    public function summary($agentId, Request $request)
    {
        $year    = $request->input('year', date('Y'));
        $payouts = AgentPayout::where('agent_id', $agentId)
            ->where('year', $year)
            ->orderBy('month')
            ->get();

        return response()->json([
            'total_policies' => $payouts->sum('total_policies'),
            'total_earnings' => $payouts->sum('net_amount'),
            'payouts'        => $payouts,
        ]);
    }

    public function downloadSlip($payoutId)
    {
        $payout = AgentPayout::with('agent.user')->findOrFail($payoutId);
        $pdf    = Pdf::loadView('admin.agents.payout-slip', compact('payout'));
        return $pdf->download('payout-slip-' . $payout->id . '.pdf');
    }
}
