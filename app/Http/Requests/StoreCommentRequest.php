<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:2', 'max:2000'],
            'commentable_type' => ['required', 'string', 'in:news,blog_post,forum_topic'],
            'commentable_id' => ['required', 'integer', 'min:1'],
            'parent_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
