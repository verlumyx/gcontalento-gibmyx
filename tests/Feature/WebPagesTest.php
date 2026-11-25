<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_successfully_with_local_assets(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Catálogo de Libros y Categorías')
            ->assertSee(route('web.books.index'))
            ->assertSee(route('web.categories.index'))
            ->assertSee('vendor/bootstrap/css/bootstrap.min.css')
            ->assertSee('vendor/bootstrap-icons/bootstrap-icons.min.css')
            ->assertSee('vendor/jquery/jquery-3.7.1.min.js')
            ->assertSee('vendor/bootstrap/js/bootstrap.bundle.min.js')
            ->assertSee('js/app.js')
            ->assertDontSee('cdn.jsdelivr.net')
            ->assertDontSee('code.jquery.com');
    }

    public function test_books_web_routes_render_successfully_with_external_scripts(): void
    {
        $category = Category::create(['name' => 'Ficción']);
        $book = Book::create([
            'title' => 'Rayuela',
            'author' => 'Julio Cortázar',
            'description' => null,
            'published_year' => 1963,
            'category_id' => $category->id,
        ]);

        $this->get('/panel/books')
            ->assertOk()
            ->assertSee('Libros')
            ->assertSee('category-filter')
            ->assertSee('js/books/index.js')
            ->assertDontSee('cdn.jsdelivr.net');

        $this->get('/panel/books/create')
            ->assertOk()
            ->assertSee('Crear Libro')
            ->assertSee('id="book-form"', false)
            ->assertSee('js/books/form.js');

        $this->get('/panel/books/'.$book->id.'/edit')
            ->assertOk()
            ->assertSee('Editar Libro #'.$book->id)
            ->assertSee('id="book-form"', false)
            ->assertSee('js/books/form.js');

        $this->get('/panel/books/9999/edit')->assertNotFound();
        $this->get('/panel/books/abc/edit')->assertNotFound();
    }

    public function test_categories_web_routes_render_successfully_with_external_scripts(): void
    {
        $category = Category::create(['name' => 'Historia']);

        $this->get('/panel/categories')
            ->assertOk()
            ->assertSee('Categorías')
            ->assertSee('categories-table-body')
            ->assertSee('js/categories/index.js')
            ->assertDontSee('cdn.jsdelivr.net');

        $this->get('/panel/categories/create')
            ->assertOk()
            ->assertSee('Crear Categoría')
            ->assertSee('id="category-form"', false)
            ->assertSee('js/categories/form.js');

        $this->get('/panel/categories/'.$category->id.'/edit')
            ->assertOk()
            ->assertSee('Editar Categoría #'.$category->id)
            ->assertSee('id="category-form"', false)
            ->assertSee('js/categories/form.js');

        $this->get('/panel/categories/9999/edit')->assertNotFound();
        $this->get('/panel/categories/abc/edit')->assertNotFound();
    }
}
