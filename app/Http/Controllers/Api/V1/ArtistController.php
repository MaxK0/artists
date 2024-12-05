<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Artist\StoreRequest;
use App\Http\Requests\Api\V1\Artist\UpdateRequest;
use App\Http\Resources\V1\Artist\ArtistCollection;
use App\Http\Resources\V1\Artist\ArtistResource;
use App\Models\Artist;
use App\Services\V1\ArtistService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArtistController extends Controller
{
    public function __construct(protected ArtistService $artistService) {}


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $artists = $this->artistService
            ->allData($request->get('perPage', 25));

        return new ArtistCollection($artists);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $artist = $this->artistService
            ->create($data);

        $dataResponse = [
            'artist' => new ArtistResource($artist)
        ];

        return response()->json(
            $dataResponse,
            Response::HTTP_CREATED
        );
    }


    /**
     * Display the specified resource.
     */
    public function show(Artist $artist)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Artist $artist)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artist $artist)
    {
        //
    }
}
