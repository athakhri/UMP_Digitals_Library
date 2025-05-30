<?php

namespace App\Http\Controllers;

use App\Models\BookAuthor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookAuthorController extends Controller
{
    public function index(): JsonResponse
    {

        $dataBookAuthor = BookAuthor::all();
        return response()->json($dataBookAuthor, 200);
    }
    // Menampilkan user berdasarkan ID
    public function show($id): JsonResponse
    {
        try {
            $dataBookAuthor = BookAuthor::findOrFail($id);
            return response()->json($dataBookAuthor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Author on this Books Data was been Not found'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|string|max:255',
            'author_id' => 'required|string|max:255',
        ]);

        $BookAuthor = BookAuthor::create([
            'book_id' => $request->book_id,
            'author_id' => $request->author_id,
        ]);

        return response()->json([
            'message' => 'Author on this Books Data was been Created.',
            'data' => $BookAuthor
        ], 201);
    }

    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $BookAuthor = BookAuthor::findOrFail($id);

            $request->validate([
                'book_id' => 'sometimes|string|max:255',
                'author_id' => 'sometimes|string|max:255',
            ]);

            // Hanya update field yang dikirim
            $data = $request->only(['book_id', 'author_id']);
            $BookAuthor->update($data);
              
  
            return response()->json([
                'message' => $BookAuthor->wasChanged()
                    ? 'Author on this Books Data was been Updated.'
                    : 'Nothing Changed in Author on this Books Data.',
                'data' => $BookAuthor
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Author on this Books Data was been Not found'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $BookAuthor = BookAuthor::findOrFail($id);
            $BookAuthor->delete();
            return response()->json(['message' => 'Author on this Books Data was been Deleted.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Author on this Books Data was been Not found.'], 404);
        }
    }
}
