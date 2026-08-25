<?php

declare(strict_types=1);

namespace App\Http\Requests\Eco;

use Illuminate\Foundation\Http\FormRequest;

class CompleteEcoTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof_text' => ['required_without:photo', 'nullable', 'string', 'max:500'],
            'photo' => ['required_without:proof_text', 'nullable', 'image', 'max:4096'],
        ];
    }
}
