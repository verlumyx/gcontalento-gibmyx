<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_categories_and_books_idempotently(): void
    {
        $this->seed(DatabaseSeeder::class);

        $firstCategoryCount = Category::count();
        $firstBookCount = Book::count();

        $this->assertGreaterThanOrEqual(5, $firstCategoryCount);
        $this->assertGreaterThanOrEqual(7, $firstBookCount);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($firstCategoryCount, Category::count());
        $this->assertSame($firstBookCount, Book::count());
    }
}
