<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'song_order',
        'album_id', 'song_id'
    ];

    protected $casts = [
        'song_order' => 'integer',
        'album_id' => 'integer',
        'song_id' => 'integer',
    ];
}
