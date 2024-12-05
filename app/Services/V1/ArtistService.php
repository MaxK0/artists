<?php

namespace App\Services\V1;

use App\Http\Resources\V1\Artist\ArtistResource;
use App\Models\Artist;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ArtistService extends BaseService
{
    public function __construct()
    {
        parent::__construct(Artist::class);
    }


    public function allData(int $perPage): LengthAwarePaginator
    {
        $artists = $this->model
            ->query()
            ->with('albums.songs', function ($q) {
                $q->ordered();
            })
            ->paginate($perPage)
            ->withQueryString();

        return $artists;
    }


    public function getArtist(Artist $artist): Artist
    {
        return $artist->load(['albums.songs' => function ($q) {
            $q->ordered();
        }]);
    }


    public function resourceResponse(Artist $artist, $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'artist' => new ArtistResource($artist)
        ], $status);
    }
}
