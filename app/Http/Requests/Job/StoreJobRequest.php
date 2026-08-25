<?php

declare(strict_types=1);

namespace App\Http\Requests\Job;

use App\Enums\JobType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'company' => ['required', 'string', 'max:160'],
            'location' => ['nullable', 'string', 'max:120'],
            'type' => ['required', Rule::enum(JobType::class)],
            'salary_min' => ['nullable', 'integer', 'min:0', 'lte:salary_max'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'tags' => ['nullable', 'array', 'max:8'],
            'tags.*' => ['string', 'max:30'],
            'description' => ['required', 'string', 'min:30', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:3000'],
            'benefits' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email'],
            'deadline' => ['nullable', 'date', 'after:today'],
        ];
    }
}
