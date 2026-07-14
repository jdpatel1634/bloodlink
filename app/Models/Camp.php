<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'camp_date',
        'start_time',
        'end_time',
        'address',
        'city_id',
        'state_id',
        'organizer',
        'facilities_available',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'camp_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the city that owns the camp.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the state that owns the camp.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * The users that belong to the camp.
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'camp_staff')
                    ->withPivot('role_in_camp')
                    ->withTimestamps();
    }

    /**
     * Get the blood units for the camp.
     */
    public function bloodUnits(): HasMany
    {
        return $this->hasMany(BloodUnit::class, 'collection_camp_id');
    }

    /**
     * Get the galleries for the camp.
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }
}
