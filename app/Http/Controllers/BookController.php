<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookController extends Controller
{
    public function index(): JsonResponse
    {

        $dataBooks = Book::all();
        return response()->json($dataBooks, 200);
    }
    // Menampilkan user berdasarkan ID
    public function show($id): JsonResponse
    {
        try {
            $books = Book::findOrFail($id);
            return response()->json($books, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Books Data was been Not found'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
            'publisher' => 'required|string|min:3',
            'year_published' => 'required|integer|min:1000|max:9999',
            'stock' => 'required|integer|min:0'
        ]);

        $books = Book::create([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'publisher' => $request->publisher,
            'year_published' => $request->year_published,
            'stock' => $request->stock
        ]);

        return response()->json([
            'message' => 'Books Data was been Created.',
            'data' => $books
        ], 201);
    }

    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $book = Book::findOrFail($id);

            $request->validate([
                'title' => 'sometimes|string|max:255',
                'isbn' => 'sometimes|string|max:255',
                'publisher' => 'sometimes|string|min:3',
                'year_published' => 'sometimes|integer|min:1000|max:9999',
                'stock' => 'sometimes|integer|min:0'
            ]);

            // Hanya update field yang dikirim
            $data = $request->only(['title', 'isbn', 'publisher', 'year_published', 'stock']);
            $book->update($data);
              
  
            return response()->json([
                'message' => $book->wasChanged()
                    ? 'Books Data was been Updated.'
                    : 'Nothing Change on Books Data.',
                'data' => $book
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Books Data was been Not found'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json(['message' => 'Books Data was been Not Deleted.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Books Data was been Not found.'], 404);
        }
    }
}
