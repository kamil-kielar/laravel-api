<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Jobs\UpdateAuthorLastBook;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = min(max($request->input('per_page', 15), 1), 100);

        $books = Book::with('authors')
            ->latest()
            ->paginate($perPage);

        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated());

        // Add Author
        $book->authors()->attach($request->input('author_ids'));
        $book->load('authors');

        UpdateAuthorLastBook::dispatch($book);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load('authors');
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());

        if ($request->has('author_ids')) {
            $book->authors()->sync($request->input('author_ids'));
        }

        $book->load('authors');
        return new BookResource($book);
    }

    /**
     * DELETE /api/books/{id}
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json([
            'message' => 'Book has been deleted successfully',
        ]);
    }
}
