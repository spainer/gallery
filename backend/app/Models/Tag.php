<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasUlids, HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'parent');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Tag::class, 'parent');
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }
}
