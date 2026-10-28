<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngestJobsRequest extends FormRequest
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
            'items.*.company' => ['required', 'string', 'max:255'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.sector' => ['required', 'string', 'max:100'],
            'items.*.location' => ['required', 'string', 'max:255'],
            'items.*.work_mode' => ['required', 'string', Rule::in(['Presencial', 'Híbrido', 'Remoto'])],
            'items.*.employment_type' => ['required', 'string', 'max:100'],
            'items.*.salary_min' => ['nullable', 'integer', 'min:0'],
            'items.*.salary_max' => ['nullable', 'integer', 'min:0', 'gte:items.*.salary_min'],
            'items.*.currency' => ['nullable', 'string', 'size:3'],
            'items.*.summary' => ['required', 'string', 'max:2000'],
            'items.*.description' => ['required', 'string', 'max:100000'],
            'items.*.apply_url' => ['required', 'url:http,https', 'max:2048'],
            'items.*.source_url' => ['required', 'url:http,https', 'max:2048'],
            'items.*.published_at' => ['nullable', 'date'],
            'items.*.expires_at' => ['nullable', 'date'],
            'items.*.metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.max' => 'Cada lote puede contener como máximo 50 vacantes.',
            'items.*.external_id.required' => 'Cada vacante necesita un identificador estable de la fuente.',
            'items.*.source_url.required' => 'Cada vacante debe conservar su URL de origen.',
            'items.*.work_mode.in' => 'La modalidad debe ser Presencial, Híbrido o Remoto.',
        ];
    }
}
