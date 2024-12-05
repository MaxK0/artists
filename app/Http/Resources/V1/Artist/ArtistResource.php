<?php

namespace App\Http\Resources\V1\Artist;

use App\Http\Resources\V1\Album\AlbumCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
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
            'name' => $this->name,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'albums' => new AlbumCollection($this->whenLoaded('albums'))
        ];
    }
}