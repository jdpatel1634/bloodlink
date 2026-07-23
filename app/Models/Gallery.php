<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image_path',
        'description',
        'camp_id',
    ];

    /**
     * Get the camp that owns the gallery image.
     */
    public function camp(): BelongsTo
    {
        return $this->belongsTo(Camp::class);
    }
}
