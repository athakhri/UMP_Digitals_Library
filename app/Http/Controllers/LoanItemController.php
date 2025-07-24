<?php

namespace App\Http\Controllers;

use App\Models\LoanItem;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Books;
use Illuminate\Support\Facades\DB;



class LoanItemController extends Controller
{
    // Tampilkan semua loan items lengkap dengan relasi
    public function index(): JsonResponse
    {
        $items = LoanItem::with('book', 'loan.user')->get();
        return response()->json($items, 200);
    }

    // Tampilkan loan item by ID
    public function show(string $id): JsonResponse
    {
        try {
            $item = LoanItem::with('book', 'loan.user')->findOrFail($id);
            return response()->json($item, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item pinjaman tidak ditemukan.'], 404);
        }
    }

public function store(Request $request, string $loan_id): JsonResponse
{
    $request->validate([
        'items' => 'required|array|min:1',
        'items.*.book_id' => 'required|string|exists:books,id',
        'items.*.quantity' => 'sometimes|integer|min:1',
    ]);

    $loan = Loans::with('user')->findOrFail($loan_id);
    $user = $loan->user;

    $items = $request->input('items');
    $totalQuantity = array_sum(array_map(fn($item) => $item['quantity'] ?? 1, $items));

    // Validasi batas berdasarkan role
    if ($user->role === 'siswa' && $totalQuantity > 3) {
        return response()->json(['message' => 'Siswa hanya boleh meminjam maksimal 3 buku.'], 422);
    }

    if ($user->role === 'guru' && $totalQuantity > 40) {
        return response()->json(['message' => 'Guru hanya boleh meminjam maksimal 40 buku.'], 422);
    }

    $loanItems = [];

    try {
        DB::beginTransaction();

        foreach ($items as $item) {
            $bookId = $item['book_id'];
            $quantity = $item['quantity'] ?? 1;

            $book = Books::findOrFail($bookId);

            if ($book->stock < $quantity) {
                throw new \Exception("Stok buku '{$book->title}' tidak mencukupi. Tersedia: {$book->stock}, diminta: {$quantity}.");
            }

            // Kurangi stok
            $book->decrement('stock', $quantity);

            // Simpan item pinjaman
            $loanItems[] = LoanItem::create([
                'loan_id' => $loan_id,
                'book_id' => $bookId,
                'due_date' => $loan->loan_date,
                'quantity' => $quantity,
            ]);
        }

        DB::commit();

        return response()->json([
            'message' => 'Item peminjaman berhasil ditambahkan.',
            'data' => $loanItems,
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Gagal menambahkan item pinjaman.',
            'error' => $e->getMessage(),
        ], 422);
    }
}



    // Update satu loan item by ID
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $item = LoanItem::findOrFail($id);

            $request->validate([
                'due_date' => 'sometimes|date|after_or_equal:today',
                'book_id'  => 'sometimes|string|exists:books,id',
                'quantity' => 'sometimes|integer|min:1',
            ]);

            $item->update($request->only(['due_date', 'book_id', 'quantity']));

            return response()->json([
                'message' => $item->wasChanged()
                    ? 'Item pinjaman diperbarui.'
                    : 'Tidak ada perubahan.',
                'data' => $item
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item tidak ditemukan.'], 404);
        }
    }

    // Hapus loan item
    public function destroy(string $id): JsonResponse
    {
        try {
            $item = LoanItem::findOrFail($id);
            $item->delete();

            return response()->json(['message' => 'Item pinjaman berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item tidak ditemukan.'], 404);
        }
    }
}
