<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_all_categories_ordered_by_id(): void
    {
        $fiction = Category::create(['name' => 'Ficción']);
        $history = Category::create(['name' => 'Historia']);

        $response = $this->getJson('/categories');

        $response->assertOk()
            ->assertExactJson([
                'data' => [
                    [
                        'id' => $fiction->id,
                        'name' => 'Ficción',
                    ],
                    [
                        'id' => $history->id,
                        'name' => 'Historia',
                    ],
                ],
            ]);
    }

    public function test_it_returns_empty_list_when_no_categories_exist(): void
    {
        $response = $this->getJson('/categories');

        $response->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_it_creates_a_category_with_valid_data(): void
    {
        $response = $this->postJson('/categories', [
            'name' => 'Ciencia ficción',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Ciencia ficción');

        $this->assertDatabaseHas('categories', [
            'name' => 'Ciencia ficción',
        ]);
    }

    public function test_create_validates_required_and_unique_name(): void
    {
        Category::create(['name' => 'Ficción']);

        $this->postJson('/categories', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $this->postJson('/categories', [
            'name' => 'Ficción',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $this->postJson('/categories', [
            'name' => str_repeat('a', 256),
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_it_updates_an_existing_category_with_put_and_patch(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        $this->putJson('/categories/'.$category->id, [
            'name' => 'Literatura',
        ])->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $category->id,
                    'name' => 'Literatura',
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Literatura',
        ]);

        $this->patchJson('/categories/'.$category->id, [
            'name' => 'Literatura',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Literatura');
    }

    public function test_update_validates_uniqueness_against_other_categories_and_returns_404(): void
    {
        $fiction = Category::create(['name' => 'Ficción']);
        $history = Category::create(['name' => 'Historia']);

        $this->putJson('/categories/'.$fiction->id, [
            'name' => 'Historia',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $this->putJson('/categories/'.$fiction->id, [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $this->putJson('/categories/9999', [
            'name' => 'Nueva',
        ])->assertNotFound();

        $this->assertDatabaseHas('categories', [
            'id' => $history->id,
            'name' => 'Historia',
        ]);
    }

    public function test_it_deletes_a_category_without_associated_books(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        $response = $this->deleteJson('/categories/'.$category->id);

        $response->assertOk()
            ->assertExactJson([
                'message' => 'Categoría eliminada correctamente.',
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_it_prevents_deleting_a_category_with_associated_books(): void
    {
        $category = Category::create(['name' => 'Ficción']);

        Book::create([
            'title' => 'El Aleph',
            'author' => 'Jorge Luis Borges',
            'description' => 'Colección de cuentos.',
            'published_year' => 1949,
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson('/categories/'.$category->id);

        $response->assertConflict()
            ->assertExactJson([
                'message' => 'No se puede eliminar la categoría porque tiene libros asociados.',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Ficción',
        ]);
    }

    public function test_it_returns_404_when_deleting_non_existent_or_non_numeric_category(): void
    {
        $this->deleteJson('/categories/9999')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Categoría no encontrada.',
            ]);

        $this->deleteJson('/categories/abc')
            ->assertNotFound();
    }

    public function test_openapi_docs_include_categories_endpoints(): void
    {
        Gate::define('viewApiDocs', fn ($user = null) => true);

        $response = $this->getJson('/docs/api.json');

        $response->assertOk()
            ->assertJsonPath('paths./categories.get.operationId', 'categories.index')
            ->assertJsonPath('paths./categories.post.operationId', 'categories.create')
            ->assertJsonMissingPath('paths./categories.post.responses.200')
            ->assertJsonPath('paths./categories.post.responses.201.description', 'Categoría creada correctamente.')
            ->assertJsonPath('components.schemas.CreateCategoryRequest.properties.name.maxLength', 255)
            ->assertJsonPath('paths./categories/{category}.put.operationId', 'categories.update')
            ->assertJsonPath('paths./categories/{category}.put.parameters.0.name', 'category')
            ->assertJsonPath('paths./categories/{category}.put.parameters.0.in', 'path')
            ->assertJsonPath('components.schemas.UpdateCategoryRequest.properties.name.maxLength', 255)
            ->assertJsonPath('paths./categories/{category}.delete.operationId', 'categories.delete')
            ->assertJsonPath('paths./categories/{category}.delete.parameters.0.name', 'category')
            ->assertJsonPath('paths./categories/{category}.delete.parameters.0.in', 'path')
            ->assertJsonPath('paths./categories/{category}.delete.responses.200.content.application/json.schema.properties.message.type', 'string')
            ->assertJsonPath('paths./categories/{category}.delete.responses.404.content.application/json.schema.properties.message.type', 'string')
            ->assertJsonPath('paths./categories/{category}.delete.responses.409.content.application/json.schema.properties.message.type', 'string');
    }
}
