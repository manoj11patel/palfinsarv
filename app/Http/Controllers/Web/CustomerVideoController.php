<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\VideoShare;

class CustomerVideoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $customer = Customer::where('email', $user->email)->first();

        $shares = $customer
            ? VideoShare::with(['video', 'sharedBy'])
                ->where('customer_id', $customer->id)
                ->whereHas('video', fn($q) => $q->where('is_active', true))
                ->latest()
                ->get()
            : collect();

        return view('customer.videos.index', compact('shares'));
    }
}
