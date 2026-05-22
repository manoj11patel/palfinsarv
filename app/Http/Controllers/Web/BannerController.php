<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->paginate(15);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'image'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_active' => 'boolean',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title'     => $request->title,
            'image_path' => $imagePath,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_active' => 'boolean',
        ]);

        $imagePath = $banner->image_path;
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image_path);
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        $banner->update([
            'title'      => $request->title,
            'image_path' => $imagePath,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();

        return back()->with('success', 'Banner deleted.');
    }
}
