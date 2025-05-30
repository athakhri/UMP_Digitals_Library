<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoanController extends Controller
{
 public function index(): JsonResponse
    {

        $dataLoans = Loan::all();
        return response()->json($dataLoans, 200);
    }
    // Menampilkan user berdasarkan ID
    public function show($id): JsonResponse
    {
        try {
            $loans = Loan::findOrFail($id);
            return response()->json($loans, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data Loans Was Been Not Found'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|string|max:255',
            'book_id' => 'required|string|max:255',
        ]);

        $loans = Loan::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);

        return response()->json([
            'message' => 'Data Loans Was Been Created.',
            'data' => $loans
        ], 201);
    }

    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $author = Loan::findOrFail($id);

            $request->validate([
                'user_id' => 'sometimes|string|max:255',
                'book_id' => 'sometimes|string|max:255',
            ]);

            // Hanya update field yang dikirim
            $data = $request->only(['user_id', 'book_id']);
            $author->update($data);
              
  
            return response()->json([
                'message' => $author->wasChanged()
                    ? 'Data Loans Was Been Updated.'
                    : 'Nothing Change on this Data Loan.',
                'data' => $author
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data Loans Was Been Not Found'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $loans = Loan::findOrFail($id);
            $loans->delete();
            return response()->json(['message' => 'Data Loans Was Been Deleted.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data Loans Was Been Not Found.'], 404);
        }
    }
}
