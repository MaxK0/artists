<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Album\AttachSongsRequest;
use App\Http\Requests\Api\V1\Album\DetachSongsRequest;
use App\Http\Requests\Api\V1\Album\StoreRequest;
use App\Http\Requests\Api\V1\Album\UpdateRequest;
use App\Http\Requests\Api\V1\Album\UpdateSongsOrderRequest;
use App\Http\Resources\V1\Album\AlbumCollection;
use App\Models\Album;
use App\Models\Song;
use App\Services\V1\AlbumService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AlbumController extends Controller
{
    public function __construct(protected AlbumService $albumService) {}


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $albums = $this->albumService
            ->allData($request->get('perPage', 25));

        return new AlbumCollection($albums);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $album = $this->albumService
            ->create($data);

        return $this->albumService
            ->resourceResponse($album, Response::HTTP_CREATED);
    }


    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        $album = $this->albumService
            ->getAlbum($album);

        return $this->albumService
            ->resourceResponse($album);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Album $album)
    {
        $data = $request->validated();

        $this->albumService
            ->update($album, $data);

        return $this->albumService
            ->resourceResponse($album);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        $this->albumService
            ->delete($album);

        return response()->noContent();
    }


    public function attachSongs(AttachSongsRequest $request, Album $album)
    {
        $data = $request->validated();

        $this->albumService
            ->attachSongs($album, $data);

        return response()->json([], Response::HTTP_CREATED);
    }


    public function detachSongs(DetachSongsRequest $request, Album $album)
    {
        $data = $request->validated();

        $this->albumService
            ->detachSongs($album, $data);

        return response()->noContent();
    }


    public function updateSongsOrder(UpdateSongsOrderRequest $request, Album $album, Song $song)
    {
        $data = $request->validated();

        $this->albumService
            ->updateSongsOrder($album, $song, $data);

        return response()->noContent();
    }
}
