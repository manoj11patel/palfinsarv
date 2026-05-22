<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function latest()
    {
        $banner = Banner::where('is_active', true)->latest()->first();

        if (! $banner) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'id'        => $banner->id,
                'title'     => $banner->title,
                'image_url' => Storage::disk('public')->url($banner->image_path),
                'created_at' => $banner->created_at->toISOString(),
            ],
        ]);
    }
}
