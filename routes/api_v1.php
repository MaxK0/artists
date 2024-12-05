<?php

use App\Http\Controllers\Api\V1\ArtistController;
use Illuminate\Support\Facades\Route;

Route::apiResource('artists', ArtistController::class);