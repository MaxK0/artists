<?php

use App\Models\Song;
use Illuminate\Http\Response;

test('Возможность получить все данные песен', function () {
    $song = Song::factory()->create();

    $this->getJson(route('api.v1.songs.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at'
                ]
            ]
        ])
        ->assertJsonFragment([
            'id' => $song->id,
            'title' => $song->title,
            'created_at' => $song->created_at->toISOString(),
            'updated_at' => $song->updated_at->toISOString()
        ]);
});

test('Возможность создать песню', function () {
    $songData = [
        'title' => 'song'
    ];

    $res = $this->postJson(route('api.v1.songs.store'), $songData)
        ->assertStatus(Response::HTTP_CREATED);

    $song = Song::first();

    expect($song)
        ->title->toBe($songData['title']);

    $res->assertJsonFragment([
        'song' => [
            'id' => $song->id,
            'title' => $song->title,
            'created_at' => $song->created_at->toISOString(),
            'updated_at' => $song->updated_at->toISOString(),
        ]
    ]);
});

test('Невозможность создать песню с неверными данными', function () {
    $songData = [
        'title' => '',
        'name' => 'name'
    ];

    $this->postJson(route('api.v1.songs.store'), $songData)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->assertDatabaseMissing('songs', [
        'title' => ''
    ]);
});

test('Возможность показать данные определенной песни', function () {
    $song = Song::factory()->create();

    $this->getJson(route('api.v1.songs.show', $song->id))
        ->assertOk()
        ->assertJsonFragment([
            'song' => [
                'id' => $song->id,
                'title' => $song->title,
                'created_at' => $song->created_at->toISOString(),
                'updated_at' => $song->updated_at->toISOString()
            ]
        ]);
});

test('Возможность обновить данные песни', function () {
    $song = Song::factory()->create();

    $songData = [
        'title' => 'song'
    ];

    $this->putJson(route('api.v1.songs.update', $song->id), $songData)
        ->assertOk()
        ->assertJsonFragment([
            'song' => [
                'id' => $song->id,
                'title' => $songData['title'],
                'created_at' => $song->created_at->toISOString(),
                'updated_at' => $song->fresh()->updated_at->toISOString()
            ]
        ]);

    expect($song)
        ->title->toBe($songData['title']);
});

test('Невозможность обновить песню с неверными данными', function () {
    $song = Song::factory()->create();

    $songData = [
        'title' => ''
    ];

    $this->putJson(route('api.v1.songs.update', $song->id), $songData)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->assertDatabaseMissing('songs', [
        'title' => ''
    ]);
});

test('Возможность удалить пользователя', function () {
    $song = Song::factory()->create();

    $this->deleteJson(route('api.v1.songs.destroy', $song->id))
        ->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing('songs', [
        'id' => $song->id
    ]);
});