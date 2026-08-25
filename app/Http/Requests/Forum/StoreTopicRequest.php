<?php

declare(strict_types=1);

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:60', 'in:umumiy,dasturlash,bandlik,ta\'lim,ekologiya,biznes,psixologiya'],
            'body' => ['required', 'string', 'min:10', 'max:10000'],
        ];
    }
}
