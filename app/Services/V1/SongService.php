<?php

namespace App\Services\V1;

use App\Http\Resources\V1\Song\SongResource;
use App\Models\Song;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SongService extends BaseService
{
    public function __construct()
    {
        parent::__construct(Song::class);
    }


    public function resourceResponse(Song $song, $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'song' => new SongResource($song)
        ], $status);
    }
}
