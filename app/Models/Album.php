<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_year',
        'artist_id'
    ];

    protected $casts = [
        'release_year' => 'integer',
        'artist_id' => 'integer'
    ];


    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }


    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class)
            ->withPivot('song_order');
    }
}
