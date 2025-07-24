<?php

namespace App\Http\Controllers;

use App\Models\BookAuthors;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookAuthorsController extends Controller
{
    public function index(): JsonResponse
    {

        $dataBookAuthor = BookAuthors::with(['book', 'author'])->get();
        return response()->json($dataBookAuthor, 200);
    }
    // Menampilkan user berdasarkan ID
    public function show($id): JsonResponse
    {
        try {
            $dataBookAuthor = BookAuthors::findOrFail($id);
            return response()->json($dataBookAuthor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data Pengarang buku Tidak Ketemu'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|string|max:255',
            'author_id' => 'required|string|max:255',
        ]);

        $BookAuthor = BookAuthors::create([
            'book_id' => $request->book_id,
            'author_id' => $request->author_id,
        ]);

        return response()->json([
            'message' => 'Data Pengarang buku berhasil ditambahkan.',
            'data' => $BookAuthor
        ], 201);
    }

    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $BookAuthor = BookAuthors::findOrFail($id);

            $request->validate([
                'book_id' => 'sometimes|string|max:255',
                'author_id' => 'sometimes|string|max:255',
            ]);

            // Hanya update field yang dikirim
            $data = $request->only(['book_id', 'author_id']);
            $BookAuthor->update($data);
              
  
            return response()->json([
                'message' => $BookAuthor->wasChanged()
                    ? 'Data Pengarang pada Buku berhasil diperbaharui.'
                    : 'Tidak ada perubahan terhadap data pengarang yang ada pada buku ini.',
                'data' => $BookAuthor
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data pengarang dari buku not found'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $BookAuthor = BookAuthors::findOrFail($id);
            $BookAuthor->delete();
            return response()->json(['message' => 'Data pengarang dari buku ini Berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data pengarang dari buku ini tidak ketemu.'], 404);
        }
    }
}
