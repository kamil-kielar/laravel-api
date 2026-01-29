<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(max($request->input('per_page', 15), 1), 100);

        $authors = Author::with('books')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->searchByBookTitle($request->input('search'));
            })
            ->latest()
            ->paginate($perPage);

        return AuthorResource::collection($authors);
    }

    /**
     * GET /api/authors/{id}
     */
    public function show(Author $author)
    {
        $author->load('books');
        return new AuthorResource($author);
    }
}
