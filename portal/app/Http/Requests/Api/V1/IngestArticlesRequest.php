<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class IngestArticlesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source' => ['required', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.external_id' => ['required', 'string', 'max:191'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.excerpt' => ['required', 'string', 'max:2000'],
            'items.*.body' => ['required', 'string', 'max:100000'],
            'items.*.category' => ['required', 'string', 'max:100'],
            'items.*.image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'items.*.source_url' => ['required', 'url:http,https', 'max:2048'],
            'items.*.published_at' => ['nullable', 'date'],
            'items.*.metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.max' => 'Cada lote puede contener como máximo 50 noticias.',
            'items.*.external_id.required' => 'Cada noticia necesita un identificador estable de la fuente.',
            'items.*.source_url.required' => 'Cada noticia debe conservar su URL de origen.',
        ];
    }
}
