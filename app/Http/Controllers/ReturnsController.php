<?php

namespace App\Http\Controllers;

use App\Models\Returns;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class ReturnsController extends Controller
{
    // Tampilkan semua pengembalian dengan transformasi
public function index(): JsonResponse
{
$returns = Returns::with(['loan.user', 'returnItems'])->get();

// Tambahkan "username" dan "return_items" ke dalam data asli
$data = $returns->map(function ($ret) {
    // Ambil array asli dari model
    $original = $ret->toArray();

    // Tambahkan username
    $original['username'] = $ret->loan->user->name ?? 'Tidak diketahui';

    // Tambahkan return_items hasil relasi
    $original['return_items'] = $ret->returnItems->map(function ($item) {
        return [
            'id' => $item->id,
            'loan_item_id' => $item->loan_item_id,
            'book_id' => $item->book_id,
            'quantity' => $item->quantity,
            'condition_note' => $item->condition_note,
        ];
    });

    return $original;
});

return response()->json($data, 200);

}


    // Tampilkan pengembalian berdasarkan ID return
    public function show(string $id): JsonResponse
    {
        try {
            $return = Returns::with('loan')->findOrFail($id);
            return response()->json($return, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data pengembalian tidak ditemukan'], 404);
        }
    }

    // Tampilkan pengembalian berdasarkan loan_id
    public function showByLoan(string $loan_id): JsonResponse
    {
        $return = Returns::where('loan_id', $loan_id)->with('loan')->first();

        if (!$return) {
            return response()->json(['message' => 'Data pengembalian tidak ditemukan untuk loan ini'], 404);
        }

        return response()->json($return, 200);
    }

    // Simpan data pengembalian baru
// POST /api/returns
public function store(Request $request): JsonResponse
{
    $request->validate([
        'loan_id' => 'required|exists:loans,id',
        'return_date' => 'required|date',
        'notes' => 'nullable|string',
    ]);

    try {
        $return = Returns::create([
            'loan_id' => $request->loan_id,
            'return_date' => $request->return_date,
            'notes' => $request->notes ?? null,
        ]);

        return response()->json([
            'message' => 'Data pengembalian berhasil disimpan',
            'id' => $return->id, // agar frontend bisa simpan return_id
            'data' => $return,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal membuat data return',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    // Update data pengembalian berdasarkan ID return
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $return = Returns::findOrFail($id);

            $request->validate([
                'return_date' => 'sometimes|date',
                'notes' => 'nullable|string',
            ]);

            if ($request->has('return_date')) {
                $return->return_date = $request->return_date;
            }

            if ($request->has('notes')) {
                $return->notes = $request->notes;
            }

            $return->save();

            return response()->json([
                'message' => 'Data pengembalian berhasil diperbarui',
                'data' => $return,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data pengembalian tidak ditemukan'], 404);
        }
    }

    // Hapus data pengembalian
    public function destroy(string $id): JsonResponse
    {
        try {
            $return = Returns::findOrFail($id);
            $return->delete();

            return response()->json(['message' => 'Data pengembalian berhasil dihapus'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data pengembalian tidak ditemukan'], 404);
        }
    }

                public function count()
    {
        $count = \App\Models\Returns::count();

        return response()->json([
            'total' => $count
        ]);
    }
}
