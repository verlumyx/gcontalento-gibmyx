<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class BooksCreateAndUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_book_with_valid_data(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        $payload = [
            'title' => 'Cien años de soledad',
            'author' => 'Gabriel García Márquez',
            'description' => 'Novela del realismo mágico.',
            'published_year' => 1967,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/books', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Cien años de soledad')
            ->assertJsonPath('data.author', 'Gabriel García Márquez')
            ->assertJsonPath('data.description', 'Novela del realismo mágico.')
            ->assertJsonPath('data.published_year', 1967)
            ->assertJsonPath('data.category_id', $category->id)
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonPath('data.category.name', 'Ficción');

        $this->assertDatabaseHas('books', $payload);
    }

    public function test_it_creates_a_book_without_optional_description(): void
    {
        $category = Category::create(['name' => 'Ciencia']);

        $response = $this->postJson('/books', [
            'title' => 'Cosmos',
            'author' => 'Carl Sagan',
            'published_year' => 1980,
            'category_id' => $category->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.description', null);

        $this->assertDatabaseHas('books', [
            'title' => 'Cosmos',
            'author' => 'Carl Sagan',
            'description' => null,
            'published_year' => 1980,
            'category_id' => $category->id,
        ]);
    }

    public function test_create_validates_required_fields(): void
    {
        $response = $this->postJson('/books', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'author',
                'published_year',
                'category_id',
            ]);
    }

    public function test_create_validates_title_length_year_and_existing_category(): void
    {
        $response = $this->postJson('/books', [
            'title' => 'Ab',
            'author' => 'Autor',
            'published_year' => (int) date('Y') + 1,
            'category_id' => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'published_year',
                'category_id',
            ]);

        $invalidNonNumericYear = $this->postJson('/books', [
            'title' => 'Libro válido',
            'author' => 'Autor',
            'published_year' => 'abc',
            'category_id' => 9999,
        ]);

        $invalidNonNumericYear->assertStatus(422)
            ->assertJsonValidationErrors(['published_year', 'category_id']);

        $tooOldYear = $this->postJson('/books', [
            'title' => 'Libro válido',
            'author' => 'Autor',
            'published_year' => 999,
            'category_id' => 9999,
        ]);

        $tooOldYear->assertStatus(422)
            ->assertJsonValidationErrors(['published_year', 'category_id']);
    }

    public function test_it_updates_an_existing_book_with_valid_data(): void
    {
        $fiction = Category::create(['name' => 'Ficción']);
        $history = Category::create(['name' => 'Historia']);

        $book = Book::create([
            'title' => 'Título original',
            'author' => 'Autor original',
            'description' => 'Descripción original',
            'published_year' => 2000,
            'category_id' => $fiction->id,
        ]);

        $response = $this->putJson('/books/'.$book->id, [
            'title' => 'Sapiens',
            'author' => 'Yuval Noah Harari',
            'description' => null,
            'published_year' => 2011,
            'category_id' => $history->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.title', 'Sapiens')
            ->assertJsonPath('data.author', 'Yuval Noah Harari')
            ->assertJsonPath('data.description', null)
            ->assertJsonPath('data.published_year', 2011)
            ->assertJsonPath('data.category_id', $history->id)
            ->assertJsonPath('data.category.name', 'Historia');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Sapiens',
            'author' => 'Yuval Noah Harari',
            'description' => null,
            'published_year' => 2011,
            'category_id' => $history->id,
        ]);
    }

    public function test_it_allows_patch_to_update_an_existing_book(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        $book = Book::create([
            'title' => 'Título original',
            'author' => 'Autor original',
            'description' => null,
            'published_year' => 2000,
            'category_id' => $category->id,
        ]);

        $response = $this->patchJson('/books/'.$book->id, [
            'title' => 'Título actualizado',
            'author' => 'Autor actualizado',
            'published_year' => 2020,
            'category_id' => $category->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Título actualizado');
    }

    public function test_update_validates_fields_and_returns_404_when_book_not_found(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        $book = Book::create([
            'title' => 'Título original',
            'author' => 'Autor original',
            'description' => null,
            'published_year' => 2000,
            'category_id' => $category->id,
        ]);

        $this->putJson('/books/9999', [
            'title' => 'Título válido',
            'author' => 'Autor válido',
            'published_year' => 2020,
            'category_id' => $category->id,
        ])->assertNotFound();

        $this->putJson('/books/'.$book->id, [
            'title' => 'No',
            'author' => '',
            'published_year' => 'invalid',
            'category_id' => 9999,
        ])->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'author',
                'published_year',
                'category_id',
            ]);
    }

    public function test_openapi_docs_include_create_and_update_book_endpoints(): void
    {
        Gate::define('viewApiDocs', fn ($user = null) => true);

        $response = $this->getJson('/docs/api.json');

        $response->assertOk()
            ->assertJsonPath('paths./books.post.operationId', 'books.create')
            ->assertJsonMissingPath('paths./books.post.responses.200')
            ->assertJsonPath('paths./books.post.responses.201.description', 'Libro creado correctamente.')
            ->assertJsonPath('paths./books/{book}.put.operationId', 'books.update')
            ->assertJsonPath('paths./books/{book}.put.parameters.0.name', 'book')
            ->assertJsonPath('paths./books/{book}.put.parameters.0.in', 'path')
            ->assertJsonPath('components.schemas.CreateBookRequest.properties.title.minLength', 3)
            ->assertJsonPath('components.schemas.UpdateBookRequest.properties.title.minLength', 3);
    }
}
