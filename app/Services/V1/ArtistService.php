<?php

namespace App\Services\V1;

use App\Models\Artist;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
                $q->orderBy('pivot_song_order');
            })
            ->paginate($perPage)
            ->withQueryString();

        return $artists;
    }
}
