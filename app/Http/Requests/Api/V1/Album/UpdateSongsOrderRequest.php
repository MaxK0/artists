<?php

namespace App\Http\Requests\Api\V1\Album;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSongsOrderRequest extends FormRequest
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
            'song_order' => ['required', 'integer', 'min:1']
        ];
    }
}
