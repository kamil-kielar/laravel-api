<?php

namespace Tests\Feature;

use App\Jobs\UpdateAuthorLastBook;
use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_requires_authentication_to_create_a_book(): void
    {
        $response = $this->postJson('/api/books', [
            'title' => 'Test',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/books', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title' ]);
    }

    /** @test */
    public function it_can_delete_a_book(): void
    {
        $book = Book::factory()
            ->hasAttached(Author::factory()->count(2))
            ->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }

    /** @test */
    public function it_requires_authentication_to_delete_a_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_list_books_with_pagination(): void
    {
        $books = Book::factory()
            ->count(25)
            ->hasAttached(Author::factory())
            ->create();

//        dd($books);

        $response = $this->getJson('/api/books?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(10, 'data');
    }
}
