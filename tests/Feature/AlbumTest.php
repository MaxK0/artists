<?php

use App\Models\Album;
use App\Models\AlbumSong;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Response;

test('Возможность получить все данные альбомов', function () {
    $album = Album::factory()->create();
    AlbumSong::factory(5)->create();

    $song = $album->songs()->first();

    $this->getJson(route('api.v1.albums.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
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
        ])
        ->assertJsonFragment([
            'id' => $album->id,
            'release_year' => $album->release_year,
            'artist_id' => $album->artist->id,
            'created_at' => $album->created_at->toISOString(),
            'updated_at' => $album->updated_at->toISOString(),
            'songs' => [
                'id' => $song->id,
                'title' => $song->title,
                'order' => $song->pivot->song_order,
                'created_at' => $song->created_at->toISOString(),
                'updated_at' => $song->updated_at->toISOString(),
            ]
        ]);
});

test('Возможность создать альбом', function () {
    $artist = Artist::factory()->create();

    $albumData = [
        'release_year' => 1000,
        'artist_id' => $artist->id
    ];

    $res = $this->postJson(route('api.v1.albums.store'), $albumData)
        ->assertStatus(Response::HTTP_CREATED);

    $album = Album::first();

    expect($album)
        ->id->toBe(1)
        ->release_year->toBe($albumData['release_year'])
        ->artist_id->toBe($artist->id);

    $res->assertJsonFragment([
        'album' => [
            'id' => $album->id,
            'release_year' => $album->release_year,
            'artist_id' => $artist->id,
            'created_at' => $album->created_at->toISOString(),
            'updated_at' => $album->updated_at->toISOString(),
        ]
    ]);
});

test('Невозможность создать альбом с неверными данными', function () {
    $albumData = [
        'release_year' => 'year',
        'name' => 'name'
    ];

    $this->postJson(route('api.v1.albums.store'), $albumData)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->assertDatabaseMissing('albums', [
        'release_year' => 'gfd'
    ]);
});

test('Возможность показать данные определенного альбома', function () {
    $album = Album::factory()->create();
    AlbumSong::factory()->create();

    $song = $album->songs()->first();

    $this->getJson(route('api.v1.albums.show', $album->id))
        ->assertOk()
        ->assertJsonFragment([
            'album' => [
                'id' => $album->id,
                'release_year' => $album->release_year,
                'artist_id' => $album->artist->id,
                'created_at' => $album->created_at->toISOString(),
                'updated_at' => $album->updated_at->toISOString(),
                'songs' => [
                    'id' => $song->id,
                    'title' => $song->title,
                    'order' => $song->pivot->song_order,
                    'created_at' => $song->created_at->toISOString(),
                    'updated_at' => $song->updated_at->toISOString(),
                ]
            ]
        ]);
});

test('Возможность обновить данные альбома', function () {
    $album = Album::factory()->create();

    $albumData = [
        'release_year' => 1000
    ];

    $this->putJson(route('api.v1.albums.update', $album->id), $albumData)
        ->assertOk()
        ->assertJsonFragment([
            'album' => [
                'id' => $album->id,
                'release_year' => $albumData['release_year'],
                'created_at' => $album->created_at->toISOString(),
                'updated_at' => $album->fresh()->updated_at->toISOString()
            ]
        ]);

    expect($album)
        ->id->toBe(1)
        ->release_year->toBe($albumData['release_year']);
});

test('Невозможность обновить альбом с неверными данными', function () {
    $album = Album::factory()->create();

    $albumData = [
        'release_year' => 'year',
        'name' => 1
    ];

    $this->putJson(route('api.v1.albums.update', $album->id), $albumData)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->assertDatabaseMissing('albums', [
        'release_year' => 'year'
    ]);
});

test('Возможность удалить альбом', function () {
    $album = Album::factory()->create();

    $this->deleteJson(route('api.v1.albums.destroy', $album->id))
        ->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing('albums', [
        'id' => $album->id
    ]);
});

test('Возможность прикрепить песни к альбому', function () {
    $album = Album::factory()->create();

    $song1 = Song::factory()->create();
    $song2 = Song::factory()->create();

    $data = [
        'song_ids' => [$song1->id, $song2->id]
    ];

    $this->postJson(route('api.v1.albums.songs.attach', $album->id), $data)
        ->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song1->id,
        'song_order' => 1
    ]);

    $this->assertdatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song2->id,
        'song_order' => 2
    ]);
});

test('Невозможность прикрепить песни с неверными данными', function () {
    $album = Album::factory()->create();

    $song = Song::factory()->create();

    $data = [
        'song_ids' => [$song->id, 5]
    ];

    $this->postJson(route('api.v1.albums.songs.attach', $album->id), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY); 

    $data['song_ids'] = $song->id;

    $this->postJson(route('api.v1.albums.songs.attach', $album->id), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY); 

    $data['song_ids'] = ['a'];

    $this->postJson(route('api.v1.albums.songs.attach', $album->id), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY); 
});

test('Невозможность прикрепить песню, которая есть в альбоме', function () {
    $album = Album::factory()->create();
    $song = Song::factory()->create();

    $album->songs()->attach($song->id);

    $data = [
        'song_ids' => [$song->id]
    ];

    $this->postJson(route('api.v1.albums.songs.attach', $album->id), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY); 
});

test('Возможность открепить песни от альбома', function () {
    $album = Album::factory()->create();
    $song = Song::factory()->create();

    $album->songs()->attach($song->id);

    $data = [
        'song_ids' => [$song->id]
    ];

    $this->postJson(route('api.v1.albums.songs.detach', $album->id), $data)
        ->assertStatus(Response::HTTP_NO_CONTENT); 

    $this->assertDatabaseMissing('album_song', [
        'album_id' => $album->id,
        'song_id' => $song->id
    ]);
});

test('Невозможность открепить песни от альбом при вводе неверных данных', function () {
    $album = Album::factory()->create();
    $song1 = Song::factory()->create();
    $song2 = Song::factory()->create();

    $album->songs()->attach($song1->id);

    $data = [
        'song_ids' => [$song2->id]
    ];

    $this->postJson(route('api.v1.albums.songs.detach', $album->id), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $data['song_ids'] = $song1->id;

    $this->postJson(route('api.v1.albums.songs.detach', $album->id), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

test('Возможность изменить порядок песни в альбоме', function () {
    $album = Album::factory()->create();
    $song1 = Song::factory()->create();
    $song2 = Song::factory()->create();
    $song3 = Song::factory()->create();

    $album->songs()->attach($song1->id, ['song_order' => 1]);
    $album->songs()->attach($song2->id, ['song_order' => 2]);
    $album->songs()->attach($song3->id, ['song_order' => 3]);

    $data = [
        'song_order' => 2
    ];

    $this->putJson(route('api.v1.albums.songs.updateOrder', [$album->id, $song1->id]), $data)
        ->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song2->id,
        'song_order' => 1
    ]);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song1->id,
        'song_order' => 2
    ]);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song3->id,
        'song_order' => 3
    ]);


    $data = [
        'song_order' => 1
    ];

    $this->putJson(route('api.v1.albums.songs.updateOrder', [$album->id, $song3->id]), $data)
        ->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song3->id,
        'song_order' => 1
    ]);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song2->id,
        'song_order' => 2
    ]);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song1->id,
        'song_order' => 3
    ]);
});

test('Возможность изменить порядок песни в альбоме при вводе значения, превашающего макс. порядок', function () {
    $album = Album::factory()->create();
    $song1 = Song::factory()->create();
    $song2 = Song::factory()->create();
    $song3 = Song::factory()->create();

    $album->songs()->attach($song1->id, ['song_order' => 1]);
    $album->songs()->attach($song2->id, ['song_order' => 2]);
    $album->songs()->attach($song3->id, ['song_order' => 3]);

    $data = [
        'song_order' => 7
    ];

    $this->putJson(route('api.v1.albums.songs.updateOrder', [$album->id, $song1->id]), $data)
        ->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song2->id,
        'song_order' => 1
    ]);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song3->id,
        'song_order' => 2
    ]);

    $this->assertDatabaseHas('album_song', [
        'album_id' => $album->id,
        'song_id' => $song3->id,
        'song_order' => 3
    ]);
});

test('Невозможность изменить порядок песни при вводе неверных данных', function () {
    $album = Album::factory()->create();
    $song1 = Song::factory()->create();
    $song2 = Song::factory()->create();
    $song3 = Song::factory()->create();

    $album->songs()->attach($song1->id, ['song_order' => 1]);
    $album->songs()->attach($song2->id, ['song_order' => 2]);
    $album->songs()->attach($song3->id, ['song_order' => 3]);

    $data = [
        'song_order' => 'a'
    ];

    $this->putJson(route('api.v1.albums.songs.updateOrder', [$album->id, $song1->id]), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $data['song_order'] = 0;

    $this->putJson(route('api.v1.albums.songs.updateOrder', [$album->id, $song1->id]), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});