<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'mobile_number',
        'gender',
        'date_of_birth',
        'address',
        'city_id',
        'state_id',
        'blood_group_id',
        'last_donation_date',
        'eligible_to_donate_until',
        'enrollment_number',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'last_donation_date' => 'date',
        'eligible_to_donate_until' => 'date',
    ];

    /**
     * Get the user that owns the donor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the blood group that owns the donor.
     */
    public function bloodGroup(): BelongsTo
    {
        return $this->belongsTo(BloodGroup::class);
    }

    /**
     * Get the city that owns the donor.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the state that owns the donor.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the blood units for the donor.
     */
    public function bloodUnits(): HasMany
    {
        return $this->hasMany(BloodUnit::class);
    }
}
