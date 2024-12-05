<?php

namespace App\Http\Requests\Api\V1\Album;

use App\Rules\SongNotAttached;
use Illuminate\Foundation\Http\FormRequest;

class AttachSongsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'song_ids' => ['required', 'array'],
            'song_ids.*' => ['required', 'integer', 'exists:songs,id', new SongNotAttached($this->album)]
        ];
    }
}
