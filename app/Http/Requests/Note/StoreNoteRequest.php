<?php

declare(strict_types=1);

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string', 'max:20000'],
            'color' => ['nullable', 'in:slate,blue,green,amber,rose'],
        ];
    }
}
