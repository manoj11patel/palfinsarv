<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'agent_user_id',
        'full_name',
        'date_of_birth',
        'anniversary_date',
        'phone',
        'email',
        'pan_number',
        'aadhar_number',
        'pan_file_path',
        'aadhar_front_file_path',
        'aadhar_back_file_path',
        'passport_file_path',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'fund_name',
        'investment_type',
        'investment_amount',
        'income_type',
        'income_details',
        'nominee_name',
        'nominee_relationship',
        'nominee_dob',
        'nominee_document_path',
        'spouse_name',
        'spouse_dob',
        'child1_name',
        'child1_dob',
        'child2_name',
        'child2_dob',
        'child3_name',
        'child3_dob',
        'status',
        'star_rating',
        'submitted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'anniversary_date' => 'date',
        'spouse_dob' => 'date',
        'child1_dob' => 'date',
        'child2_dob' => 'date',
        'child3_dob' => 'date',
        'nominee_dob' => 'date',
        'submitted_at' => 'datetime',
        'investment_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'customer_product');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }
}
