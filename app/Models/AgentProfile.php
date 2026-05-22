<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AgentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
        'phone',
        'date_of_birth',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payouts()
    {
        return $this->hasMany(AgentPayout::class, 'agent_id');
    }
}
