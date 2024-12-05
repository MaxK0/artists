<?php

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Response;

test('Возможность получить все данные исполнителей', function () {
    $artist = Artist::factory()->create();
    $album = Album::factory()->create([
        'artist_id' => $artist->id
    ]);
    $song = Song::factory()->create();

    $album->songs()->attach($song->id, ['song_order' => 1]);
    $song = $album->songs()->first();

    $this->getJson(route('api.v1.artists.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                    'albums' => [
                        '*' => [
                            'id',
                            'release_year',
                            'artist_id',
                            'created_at',
                            'updated_at',
                            'songs' => [
                                '*' => [
                                    'id',
                                    'title',
                                    'order',
                                    'created_at',
                                    'updated_at'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ])
        ->assertJsonFragment([
            'id' => $artist->id,
            'name' => $artist->name,
            'created_at' => $artist->created_at->toISOString(),
            'updated_at' => $artist->updated_at->toISOString(),
            'albums' => [
                [
                    'id' => $album->id,
                    'release_year' => $album->release_year,
                    'artist_id' => $artist->id,
                    'created_at' => $album->created_at->toISOString(),
                    'updated_at' => $album->updated_at->toISOString(),
                    'songs' => [
                        [
                            'id' => $song->id,
                            'title' => $song->title,
                            'order' => $song->pivot->song_order,
                            'created_at' => $song->created_at->toISOString(),
                            'updated_at' => $song->updated_at->toISOString()
                        ]
                    ]
                ]
            ]
        ]);
});

test('Возможность создать исполнителя', function () {
    $artistData = [
        'name' => 'Ivan'
    ];

    $res = $this->postJson(route('api.v1.artists.store'), $artistData)
        ->assertStatus(Response::HTTP_CREATED);

    $artist = Artist::first();

    expect($artist)
        ->name->toBe($artistData['name']);

    $res->assertJsonFragment([
        'artist' => [
            'id' => $artist->id,
            'name' => $artist->name,
            'created_at' => $artist->created_at->toISOString(),
            'updated_at' => $artist->updated_at->toISOString(),
        ]
    ]);
});

test('Невозможность создать исполнителя с неверными данными', function () {
    $artistData = [
        'name' => ''
    ];

    $this->postJson(route('api.v1.artists.store'), $artistData)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->assertDatabaseMissing('artists', [
        'name' => ''
    ]);
});

test('Возможность показать данные определенного исполнителя', function () {
    $artist = Artist::factory()->create();
    $album = Album::factory()->create([
        'artist_id' => $artist->id
    ]);
    $song = Song::factory()->create();

    $album->songs()->attach($song->id, ['song_order' => 1]);
    $song = $album->songs()->first();

    $this->getJson(route('api.v1.artists.show', $artist->id))
        ->assertOk()
        ->assertJsonFragment([
            'artist' => [
                'id' => $artist->id,
                'name' => $artist->name,
                'created_at' => $artist->created_at->toISOString(),
                'updated_at' => $artist->updated_at->toISOString(),
                'albums' => [
                    [
                        'id' => $album->id,
                        'release_year' => $album->release_year,
                        'artist_id' => $artist->id,
                        'created_at' => $album->created_at->toISOString(),
                        'updated_at' => $album->updated_at->toISOString(),
                        'songs' => [
                            [
                                'id' => $song->id,
                                'title' => $song->title,
                                'order' => $song->pivot->song_order,
                                'created_at' => $song->created_at->toISOString(),
                                'updated_at' => $song->updated_at->toISOString()
                            ]
                        ]
                    ]
                ]
            ]
        ]);
});

test('Возможность обновить исполнителя', function () {
    $artist = Artist::factory()->create([
        'name' => 'Max'
    ]);

    $updateArtistData = [
        'name' => 'Ivan'
    ];

    $this->putJson(route('api.v1.artists.update', $artist->id), $updateArtistData)
        ->assertOk()
        ->assertJsonFragment([
            'artist' => [
                'id' => $artist->id,
                'name' => $updateArtistData['name'],
                'created_at' => $artist->created_at->toISOString(),
                'updated_at' => $artist->fresh()->updated_at->toISOString(),
            ]
        ]);

    expect($artist)
        ->name->toBe($updateArtistData['name']);
});

test('Невозможность обновить исполнителя с неверными данными', function () {
    $artist = Artist::factory()->create();

    $updateArtistData = [
        'name' => ''
    ];

    $this->putJson(route('api.v1.artists.update', $artist->id), $updateArtistData)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->assertDatabaseMissing('artists', [
        'name' => ''
    ]);
});

test('Возможность удалить исполнителя', function () {
    $artist = Artist::factory()->create();

    $this->deleteJson(route('api.v1.artists.destroy', $artist->id))
        ->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing('artists', [
        'id' => $artist->id
    ]);
});
