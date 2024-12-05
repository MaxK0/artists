<?php

use App\Http\Controllers\Api\V1\ArtistController;
use App\Http\Controllers\Api\V1\SongController;
use Illuminate\Support\Facades\Route;

Route::apiResource('artists', ArtistController::class);
Route::apiResource('songs', SongController::class);