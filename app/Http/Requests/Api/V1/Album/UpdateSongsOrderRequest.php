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
}
