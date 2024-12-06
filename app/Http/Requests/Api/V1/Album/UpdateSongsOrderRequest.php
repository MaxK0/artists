<?php

namespace App\Http\Requests\Api\V1\Album;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSongsOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'song_order' => ['required', 'integer', 'min:1']
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->album->songs()->where('songs.id', $this->song->id)->exists()) {
                    $validator->errors()->add(
                        'song',
                        'Песни нет в альбоме'
                    );
                };
            }
        ];
    }
}
