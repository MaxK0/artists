<?php

namespace Database\Seeders;

use App\Models\AlbumSong;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlbumSongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AlbumSong::factory()->count(30)->create();
    }
}
