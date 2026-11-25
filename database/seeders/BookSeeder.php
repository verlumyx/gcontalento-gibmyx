<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'name');

        if ($categories->isEmpty()) {
            return;
        }

        $books = [
            [
                'title' => 'Cien años de soledad',
                'author' => 'Gabriel García Márquez',
                'description' => 'Novela emblemática del realismo mágico que narra la historia de la familia Buendía en Macondo.',
                'published_year' => 1967,
                'category' => 'Ficción',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'description' => 'Distopía clásica sobre vigilancia estatal y control del pensamiento.',
                'published_year' => 1949,
                'category' => 'Ficción',
            ],
            [
                'title' => 'Cosmos',
                'author' => 'Carl Sagan',
                'description' => 'Obra de divulgación científica sobre el universo, la astronomía y la historia de la ciencia.',
                'published_year' => 1980,
                'category' => 'Ciencia',
            ],
            [
                'title' => 'Breve historia del tiempo',
                'author' => 'Stephen Hawking',
                'description' => 'Introducción accesible a la cosmología moderna, los agujeros negros y el origen del universo.',
                'published_year' => 1988,
                'category' => 'Ciencia',
            ],
            [
                'title' => 'Sapiens: De animales a dioses',
                'author' => 'Yuval Noah Harari',
                'description' => 'Recorrido por la historia de la humanidad desde la Edad de Piedra hasta el siglo XXI.',
                'published_year' => 2011,
                'category' => 'Historia',
            ],
            [
                'title' => 'Código limpio',
                'author' => 'Robert C. Martin',
                'description' => 'Manual de estilo y buenas prácticas para el desarrollo ágil de software.',
                'published_year' => 2008,
                'category' => 'Tecnología',
            ],
            [
                'title' => 'El mundo de Sofía',
                'author' => 'Jostein Gaarder',
                'description' => 'Novela sobre la historia de la filosofía occidental.',
                'published_year' => 1991,
                'category' => 'Filosofía',
            ],
        ];

        foreach ($books as $bookData) {
            $categoryId = $categories->get($bookData['category']) ?? $categories->first();

            Book::firstOrCreate(
                [
                    'title' => $bookData['title'],
                    'author' => $bookData['author'],
                ],
                [
                    'description' => $bookData['description'],
                    'published_year' => $bookData['published_year'],
                    'category_id' => $categoryId,
                ],
            );
        }
    }
}
