<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'customer_id',
        'agent_user_id',
        'product_id',
        'status',
        'profile_payload',
        'submitted_at',
        'verified_at',
        'converted_at',
    ];

    protected $casts = [
        'profile_payload' => 'array',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    protected function profilePayload(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_string($value)) {
                    return json_decode($value, true) ?? [];
                }
                return $value ?? [];
            },
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                return $value;
            }
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
