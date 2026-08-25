<?php

declare(strict_types=1);

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class EnrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course' => ['required', 'exists:courses,id,is_published,1'],
        ];
    }
}
