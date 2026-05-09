<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url'],
            'interval' => ['required', 'integer', 'min:1'],
            'timeout' => ['required', 'integer', 'min:1'],
            'method' => ['required', 'in:GET,HEAD'],
        ];
    }
}