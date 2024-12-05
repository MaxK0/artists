<?php

namespace App\Rules;

use App\Models\Album;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SongNotAttached implements ValidationRule
{
    public function __construct (protected Album $album) {}


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->album->songs()->where('songs.id', $value)->exists()) {
            $fail('Песня :value уже прикреплена к этому альбому.');
        }
    }
}
