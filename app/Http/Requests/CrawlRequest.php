<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrawlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url']
        ];
    }
}
