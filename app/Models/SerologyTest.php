<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerologyTest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'blood_unit_id',
        'test_type',
        'result',
        'test_date',
        'tested_by_user_id',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'test_date' => 'date',
    ];

    /**
     * Get the blood unit that owns the serology test.
     */
    public function bloodUnit(): BelongsTo
    {
        return $this->belongsTo(BloodUnit::class);
    }

    /**
     * Get the user that performed the test.
     */
    public function testedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by_user_id');
    }
}
