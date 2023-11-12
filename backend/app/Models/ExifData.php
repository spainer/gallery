<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExifData extends Model
{
    use HasFactory;

    protected $touches = ['image'];

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
