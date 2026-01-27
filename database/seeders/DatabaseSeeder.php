<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create test Authors and Books
        $authors = Author::factory()->count(20)->create();

        Book::factory()
            ->count(50)
            ->create()
            ->each(function ($book) use ($authors) {
                $book->authors()->attach(
                    $authors->random(rand(1, 3))->pluck('id')->toArray()
                );
            });
    }
}
