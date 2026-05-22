<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Video;
use App\Models\VideoShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with('creator')->latest()->paginate(20);
        return view('admin.settings.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.settings.videos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'video_type'   => 'required|in:upload,youtube,url',
            'video_url'    => 'required_if:video_type,youtube,url|nullable|url',
            'video_file'   => 'required_if:video_type,upload|nullable|file|mimes:mp4,mov,avi,webm|max:204800',
            'thumbnail'    => 'nullable|image|max:5120',
            'is_active'    => 'boolean',
        ]);

        $videoPath = null;
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('videos', 'public');
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('video-thumbnails', 'public');
        }

        Video::create([
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'video_type'     => $data['video_type'],
            'video_url'      => in_array($data['video_type'], ['youtube', 'url']) ? ($data['video_url'] ?? null) : null,
            'video_path'     => $videoPath,
            'thumbnail_path' => $thumbnailPath,
            'is_active'      => $request->boolean('is_active', true),
            'created_by'     => auth()->id(),
        ]);

        return redirect()->route('admin.settings.videos.index')->with('success', 'Video added successfully.');
    }

    public function edit(Video $video)
    {
        return view('admin.settings.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'video_type'   => 'required|in:upload,youtube,url',
            'video_url'    => 'required_if:video_type,youtube,url|nullable|url',
            'video_file'   => 'nullable|file|mimes:mp4,mov,avi,webm|max:204800',
            'thumbnail'    => 'nullable|image|max:5120',
            'is_active'    => 'boolean',
        ]);

        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            if ($video->video_path) {
                Storage::disk('public')->delete($video->video_path);
            }
            $data['video_path'] = $request->file('video_file')->store('videos', 'public');
        }

        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail_path) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('video-thumbnails', 'public');
        }

        $video->update([
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'video_type'     => $data['video_type'],
            'video_url'      => in_array($data['video_type'], ['youtube', 'url']) ? ($data['video_url'] ?? null) : null,
            'video_path'     => $data['video_path'] ?? $video->video_path,
            'thumbnail_path' => $data['thumbnail_path'] ?? $video->thumbnail_path,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.settings.videos.index')->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        if ($video->video_path) {
            Storage::disk('public')->delete($video->video_path);
        }
        if ($video->thumbnail_path) {
            Storage::disk('public')->delete($video->thumbnail_path);
        }
        $video->delete();

        return back()->with('success', 'Video deleted.');
    }

    public function share(Request $request, Video $video)
    {
        $data = $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'note' => 'nullable|string|max:500',
        ]);

        $sharedBy = auth()->id();
        $count = 0;

        foreach ($data['customer_ids'] as $customerId) {
            VideoShare::updateOrCreate(
                ['video_id' => $video->id, 'customer_id' => $customerId],
                ['shared_by' => $sharedBy, 'note' => $data['note'] ?? null]
            );
            $count++;
        }

        return back()->with('success', "Video shared with {$count} customer(s).");
    }
}
