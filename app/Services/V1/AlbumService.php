<?php

namespace App\Services\V1;

use App\Http\Resources\V1\Album\AlbumResource;
use App\Models\Album;
use App\Models\Song;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AlbumService extends BaseService
{
    public function __construct()
    {
        parent::__construct(Album::class);
    }


    public function allData(int $perPage): LengthAwarePaginator
    {
        $albums = $this->model
            ->query()
            ->with('songs', function ($q) {
                $q->ordered();
            })
            ->paginate($perPage)
            ->withQueryString();

        return $albums;
    }


    public function getAlbum(Album $album): Album
    {
        $album->load(['songs' => function ($q) {
            $q->ordered();
        }]);

        return $album;
    }


    public function attachSongs(Album $album, array $data): void
    {
        $order = $album->songs()->exists() ? $album->songs()->max('song_order') : 1;

        $dataToAttach = [];

        foreach ($data['song_ids'] as $songId) {
            $dataToAttach[$songId] = ['song_order' => $order];
            $order++;
        }

        $album
            ->songs()
            ->attach($dataToAttach);
    }


    public function detachSongs(Album $album, array $data): void
    {
        $album
            ->songs()
            ->detach($data['song_ids']);
    }


    public function updateSongsOrder(Album $album, Song $song, array $data): void
    {
        $currentSongOrder = $album->songs()->where('songs.id', $song->id)->first()->pivot->song_order;
        $newSongOrder = $data['song_order'];

        if ($currentSongOrder === $newSongOrder) return;

        if ($currentSongOrder < $newSongOrder) {
            $songs = $album->songs()
                ->where('song_order', '>', $currentSongOrder)
                ->where('song_order', '<=', $newSongOrder)
                ->get();

            foreach ($songs as $curSong) {
                $album->songs()->updateExistingPivot($curSong->id, ['song_order' => --$curSong->pivot->song_order]);
            }
        } else {
            $songs = $album->songs()
                ->where('song_order', '<', $currentSongOrder)
                ->where('song_order', '>=', $newSongOrder)
                ->get();

            foreach ($songs as $curSong) {
                $album->songs()->updateExistingPivot($curSong->id, ['song_order' => ++$curSong->pivot->song_order]);
            }
        }

        $maxOrder = $album->songs()->max('song_order');

        if ($newSongOrder > $maxOrder) {
            $album->songs()->updateExistingPivot($song->id, ['song_order' => ++$maxOrder]);
        } else {
            $album->songs()->updateExistingPivot($song->id, ['song_order' => $newSongOrder]);
        }
    }


    public function resourceResponse(Album $album, $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'album' => new AlbumResource($album)
        ], $status);
    }
}
