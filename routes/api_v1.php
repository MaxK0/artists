<?php

use App\Http\Controllers\Api\V1\AlbumController;
use App\Http\Controllers\Api\V1\ArtistController;
use App\Http\Controllers\Api\V1\SongController;
use Illuminate\Support\Facades\Route;

Route::apiResource('artists', ArtistController::class);

Route::apiResource('songs', SongController::class);

Route::apiResource('albums', AlbumController::class);

Route::prefix('/albums/{album}/songs')->name('albums.songs.')->group(function () {
    Route::post('/attach', [AlbumController::class, 'attachSongs'])->name('attach');
    Route::post('/detach', [AlbumController::class, 'detachSongs'])->name('detach');
    Route::put('/{song}/updateOrder', [AlbumController::class, 'updateSongsOrder'])->name('updateOrder');
}); 