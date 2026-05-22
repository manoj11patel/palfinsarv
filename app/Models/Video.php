<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_type',
        'video_path',
        'video_url',
        'thumbnail_path',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shares()
    {
        return $this->hasMany(VideoShare::class);
    }

    public function sharedCustomers()
    {
        return $this->hasManyThrough(Customer::class, VideoShare::class, 'video_id', 'id', 'id', 'customer_id');
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->video_type === 'youtube' && $this->video_url) {
            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->video_url, $m);
            return isset($m[1]) ? 'https://www.youtube.com/embed/' . $m[1] : null;
        }
        return null;
    }
}
