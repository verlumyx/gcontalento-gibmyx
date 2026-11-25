<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class BooksIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_all_stored_books_as_json(): void
    {
        $fiction = Category::create(['name' => 'Ficción']);
        $history = Category::create(['name' => 'Historia']);

        $bookA = Book::create([
            'title' => 'Cien años de soledad',
            'author' => 'Gabriel García Márquez',
            'description' => 'Novela del realismo mágico.',
            'published_year' => 1967,
            'category_id' => $fiction->id,
        ]);

        $bookB = Book::create([
            'title' => 'Sapiens',
            'author' => 'Yuval Noah Harari',
            'description' => null,
            'published_year' => 2011,
            'category_id' => $history->id,
        ]);

        $response = $this->getJson('/books');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'id' => $bookA->id,
                'title' => 'Cien años de soledad',
                'author' => 'Gabriel García Márquez',
                'published_year' => 1967,
                'category_id' => $fiction->id,
                'category' => [
                    'id' => $fiction->id,
                    'name' => 'Ficción',
                ],
            ])
            ->assertJsonFragment([
                'id' => $bookB->id,
                'title' => 'Sapiens',
                'description' => null,
                'category_id' => $history->id,
                'category' => [
                    'id' => $history->id,
                    'name' => 'Historia',
                ],
            ]);
    }

    public function test_it_filters_books_by_category_query_parameter(): void
    {
        $fiction = Category::create(['name' => 'Ficción']);
        $science = Category::create(['name' => 'Ciencia']);

        $matchingBook = Book::create([
            'title' => 'El Aleph',
            'author' => 'Jorge Luis Borges',
            'description' => 'Colección de cuentos.',
            'published_year' => 1949,
            'category_id' => $fiction->id,
        ]);

        Book::create([
            'title' => 'Cosmos',
            'author' => 'Carl Sagan',
            'description' => 'Divulgación científica.',
            'published_year' => 1980,
            'category_id' => $science->id,
        ]);

        $response = $this->getJson('/books?category='.$fiction->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingBook->id)
            ->assertJsonPath('data.0.category_id', $fiction->id);
    }

    public function test_it_returns_empty_list_when_no_books_match_category(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'description' => null,
            'published_year' => 1949,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/books?category=9999');

        $response->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_it_returns_all_books_when_category_query_parameter_is_empty(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'description' => null,
            'published_year' => 1949,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/books?category=');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_it_validates_category_query_parameter_is_positive_integer(): void
    {
        $response = $this->getJson('/books?category=abc');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }

    public function test_it_documents_books_index_endpoint_in_openapi(): void
    {
        Gate::define('viewApiDocs', fn ($user = null) => true);

        $response = $this->getJson('/docs/api.json');

        $response->assertOk()
            ->assertJsonPath('paths./books.get.operationId', 'books.index')
            ->assertJsonPath('paths./books.get.parameters.0.name', 'category')
            ->assertJsonPath('paths./books.get.parameters.0.in', 'query');
    }
}
