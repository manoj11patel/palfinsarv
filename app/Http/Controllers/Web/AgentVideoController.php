<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Video;
use App\Models\VideoShare;
use Illuminate\Http\Request;

class AgentVideoController extends Controller
{
    public function index()
    {
        $videos = Video::where('is_active', true)->latest()->get();
        $customers = Customer::where('agent_user_id', auth()->id())
            ->orderBy('full_name')
            ->get();

        return view('agent.videos.index', compact('videos', 'customers'));
    }

    public function share(Request $request, Video $video)
    {
        $agentId = auth()->id();

        $data = $request->validate([
            'customer_ids'   => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'note'           => 'nullable|string|max:500',
        ]);

        // Ensure agent only shares with their own customers
        $allowedIds = Customer::where('agent_user_id', $agentId)
            ->whereIn('id', $data['customer_ids'])
            ->pluck('id');

        $count = 0;
        foreach ($allowedIds as $customerId) {
            VideoShare::updateOrCreate(
                ['video_id' => $video->id, 'customer_id' => $customerId],
                ['shared_by' => $agentId, 'note' => $data['note'] ?? null]
            );
            $count++;
        }

        return back()->with('success', "Video shared with {$count} customer(s).");
    }
}
