<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BookRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            /**
             * Título del libro.
             *
             * @example Cien años de soledad
             */
            'title' => ['required', 'string', 'min:3', 'max:255'],
            /**
             * Nombre del autor del libro.
             *
             * @example Gabriel García Márquez
             */
            'author' => ['required', 'string', 'max:255'],
            /**
             * Descripción o sinopsis opcional del libro.
             *
             * @example Novela emblemática del realismo mágico.
             */
            'description' => ['nullable', 'string'],
            /**
             * Año de publicación del libro.
             *
             * @example 1967
             */
            'published_year' => ['required', 'integer', 'min:1000', 'max:'.date('Y')],
            /**
             * ID de la categoría a la que pertenece el libro.
             *
             * @example 1
             */
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }
}
