<?php

namespace App\Http\Controllers;

use App\Models\Authors;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorsController extends Controller
{
    public function index(): JsonResponse
    {

        $dataAuthor = Authors::all();
        return response()->json($dataAuthor, 200);
    }
    // Menampilkan user berdasarkan ID
    public function show($id): JsonResponse
    {
        try {
            $author = Authors::findOrFail($id);
            return response()->json($author, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Author Tidak Ketemu'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'birthdate' => 'required|string|min:8',
        ]);

        $author = Authors::create([
            'name' => $request->name,
            'nationality' => $request->nationality,
            'birthdate' => $request->birthdate,
        ]);

        return response()->json([
            'message' => 'Dara Author berhasil ditambahkan.',
            'data' => $author
        ], 201);
    }

    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $author = Authors::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'nationality' => 'sometimes|string|max:255',
                'birthdate' => 'sometimes|string|min:8',
            ]);

            // Hanya update field yang dikirim
            $data = $request->only(['name', 'nationality', 'birthdate']);
            $author->update($data);
              
  
            return response()->json([
                'message' => $author->wasChanged()
                    ? 'Data Author berhasil diupdate.'
                    : 'Tidak ada perubahan pada data Author.',
                'data' => $author
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Author not found'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $author = Authors::findOrFail($id);
            $author->delete();
            return response()->json(['message' => 'Author berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Author tidak ditemukan.'], 404);
        }
    }
}
