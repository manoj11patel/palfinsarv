<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'month',
        'year',
        'total_policies',
        'total_amount',
        'commission',
        'deductions',
        'net_amount',
    ];

    public function agent()
    {
        return $this->belongsTo(AgentProfile::class, 'agent_id');
    }
}
