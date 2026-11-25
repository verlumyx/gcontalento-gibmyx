<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class BooksDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_an_existing_book_and_returns_json(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        $book = Book::create([
            'title' => 'El Aleph',
            'author' => 'Jorge Luis Borges',
            'description' => 'Colección de cuentos.',
            'published_year' => 1949,
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson('/books/'.$book->id);

        $response->assertOk()
            ->assertExactJson([
                'message' => 'Libro eliminado correctamente.',
            ]);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_it_returns_404_when_deleting_a_non_existent_book(): void
    {
        $response = $this->deleteJson('/books/9999');

        $response->assertNotFound()
            ->assertExactJson([
                'message' => 'Libro no encontrado.',
            ]);
    }

    public function test_it_returns_404_for_non_numeric_book_id(): void
    {
        $this->deleteJson('/books/abc')->assertNotFound();
    }

    public function test_openapi_docs_include_delete_book_endpoint(): void
    {
        Gate::define('viewApiDocs', fn ($user = null) => true);

        $response = $this->getJson('/docs/api.json');

        $response->assertOk()
            ->assertJsonPath('paths./books/{book}.delete.operationId', 'books.delete')
            ->assertJsonPath('paths./books/{book}.delete.parameters.0.name', 'book')
            ->assertJsonPath('paths./books/{book}.delete.parameters.0.in', 'path')
            ->assertJsonPath('paths./books/{book}.delete.responses.200.content.application/json.schema.properties.message.type', 'string')
            ->assertJsonPath('paths./books/{book}.delete.responses.404.content.application/json.schema.properties.message.type', 'string');
    }
}
