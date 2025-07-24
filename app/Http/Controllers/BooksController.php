<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BooksController extends Controller
{
    public function index(): JsonResponse
    {

        $dataBooks = Books::all();
        return response()->json($dataBooks, 200);
    }
    // Menampilkan user berdasarkan ID
    public function show($id): JsonResponse
    {
        try {
            $books = Books::findOrFail($id);
            return response()->json($books, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Buku Tidak Ketemu'], 404);
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

        $books = Books::create([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'publisher' => $request->publisher,
            'year_published' => $request->year_published,
            'stock' => $request->stock
        ]);

        return response()->json([
            'message' => 'Data Buku berhasil ditambahkan.',
            'data' => $books
        ], 201);
    }

    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $book = Books::findOrFail($id);

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
                    ? 'Data Buku berhasil diupdate.'
                    : 'Tidak ada perubahan pada data Buku.',
                'data' => $book
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Buku not found'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $book = Books::findOrFail($id);
            $book->delete();
            return response()->json(['message' => 'Buku berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Buku tidak ditemukan.'], 404);
        }
    }
            public function count()
    {
        $count = \App\Models\Books::count();

        return response()->json([
            'total' => $count
        ]);
    }
}
