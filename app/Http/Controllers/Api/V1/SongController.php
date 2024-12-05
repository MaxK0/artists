<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Song\StoreRequest;
use App\Http\Requests\Api\V1\Song\UpdateRequest;
use App\Http\Resources\V1\Song\SongCollection;
use App\Http\Resources\V1\Song\SongResource;
use App\Models\Song;
use App\Services\V1\SongService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SongController extends Controller
{
    public function __construct(protected SongService $songService) {}


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $songs = $this->songService
            ->allWithPaginate($request->get('perPage', 25));

        return new SongCollection($songs);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $song = $this->songService
            ->create($data);

        return $this->songService
            ->resourceResponse($song, Response::HTTP_CREATED);
    }


    /**
     * Display the specified resource.
     */
    public function show(Song $song)
    {
        return $this->songService
            ->resourceResponse($song);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Song $song)
    {
        $data = $request->validated();

        $this->songService
            ->update($song, $data);

        return $this->songService
            ->resourceResponse($song->refresh());
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Song $song)
    {
        $this->songService
            ->delete($song);

        return response()->noContent();
    }
}
