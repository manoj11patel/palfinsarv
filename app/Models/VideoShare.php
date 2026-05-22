<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoShare extends Model
{
    protected $fillable = ['video_id', 'shared_by', 'customer_id', 'note'];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
