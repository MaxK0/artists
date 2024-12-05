<?php

namespace App\Http\Resources\V1\Album;

use App\Http\Resources\V1\Song\SongCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'release_year' => $this->release_year,
            'artist_id' => $this->artist_id,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'songs' => new SongCollection($this->whenLoaded('songs')),
        ];
    }
}
