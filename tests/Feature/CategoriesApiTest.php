<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CategoriesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_a_single_category_and_book(): void
    {
        $category = Category::create(['name' => 'Ciencia']);
        $book = Book::create([
            'title' => 'Cosmos',
            'author' => 'Carl Sagan',
            'description' => null,
            'published_year' => 1980,
            'category_id' => $category->id,
        ]);

        $this->getJson('/categories/'.$category->id)
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', 'Ciencia');

        $this->getJson('/books/'.$book->id)
            ->assertOk()
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.title', 'Cosmos')
            ->assertJsonPath('data.category.name', 'Ciencia');

        $this->getJson('/categories/9999')->assertNotFound();
        $this->getJson('/books/9999')->assertNotFound();
    }

    public function test_openapi_docs_include_show_endpoints(): void
    {
        Gate::define('viewApiDocs', fn ($user = null) => true);

        $this->getJson('/docs/api.json')
            ->assertOk()
            ->assertJsonPath('paths./books/{book}.get.operationId', 'books.show')
            ->assertJsonPath('paths./categories/{category}.get.operationId', 'categories.show');
    }
}
