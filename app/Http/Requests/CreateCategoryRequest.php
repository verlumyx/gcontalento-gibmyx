<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            /**
             * Nombre único de la categoría.
             *
             * @example Ciencia Ficción
             */
            'name' => ['required', 'string', 'min:2', 'max:255', 'unique:categories,name'],
        ];
    }
}
