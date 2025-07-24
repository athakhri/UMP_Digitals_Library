<?php

namespace App\Http\Controllers;

use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Books;
use App\Models\Returns;
use App\Models\LoanItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ReturnItemController extends Controller
{
    // Tampilkan semua return items lengkap dengan relasi
    public function index(): JsonResponse
    {
                Log::info("✅ Log Laravel aktif dan berjalan normal");

        $items = ReturnItem::with('loanItem', 'return', 'book')->get();
        return response()->json($items, 200);
    }

    // Tampilkan return item by ID
    public function show(string $id): JsonResponse
    {
        try {
            $item = ReturnItem::with('loanItem', 'return', 'book')->findOrFail($id);
            return response()->json($item, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Return item tidak ditemukan.'], 404);
        }
    }

    // Simpan return items baru (multiple sekaligus)
    public function store(Request $request, string $return_id): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.loan_item_id' => 'required|string|exists:loan_items,id',
            'items.*.book_id' => 'required|string|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition_note' => 'nullable|string',
        ]);

        $returnItems = [];

        foreach ($request->items as $item) {
            $returnItems[] = ReturnItem::create([
                'return_id' => $return_id,
                'loan_item_id' => $item['loan_item_id'],
                'book_id' => $item['book_id'],
                'quantity' => $item['quantity'],
                'condition_note' => $item['condition_note'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Return items berhasil ditambahkan.',
            'data' => $returnItems,
        ], 201);
    }

    // Update satu return item
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $item = ReturnItem::findOrFail($id);

            $request->validate([
                'quantity' => 'sometimes|integer|min:1',
                'condition_note' => 'nullable|string',
            ]);

            $item->update($request->only(['quantity', 'condition_note']));

            return response()->json([
                'message' => $item->wasChanged()
                    ? 'Return item diperbarui.'
                    : 'Tidak ada perubahan.',
                'data' => $item,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Return item tidak ditemukan.'], 404);
        }
    }

    // Hapus return item
    public function destroy(string $id): JsonResponse
    {
        try {
            $item = ReturnItem::findOrFail($id);
            $item->delete();

            return response()->json(['message' => 'Return item berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Return item tidak ditemukan.'], 404);
        }
    }

    // Tampilkan berdasarkan return_id
    public function showByReturnId(string $return_id): JsonResponse
    {
        $items = ReturnItem::with('loanItem', 'return', 'book')
            ->where('return_id', $return_id)
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['message' => 'Return item tidak ditemukan.'], 404);
        }

        return response()->json($items, 200);
    }

    // Simpan return item + update stok + cek status
public function saveReturnItems(Request $request, string $return_id): JsonResponse
{
    Log::info("➡️ saveReturnItems() dipanggil untuk return_id: $return_id");

    $request->validate([
        'items' => 'required|array|min:1',
        'items.*.loan_item_id' => 'required|exists:loan_items,id',
        'items.*.book_id' => 'required|exists:books,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.condition_note' => 'nullable|string',
    ]);
     Log::info("🟡 Data items yang dikirim dari frontend:", $request->items);
     Log::info("🟡 JSON items:", ['items' => json_encode($request->items)]);


    $return = Returns::findOrFail($return_id);
    $loan = $return->loan;

    Log::info("✅ Data return dan loan berhasil ditemukan. Loan ID: {$loan->id}");

    DB::beginTransaction();

    try {
        foreach ($request->items as $item) {
            Log::info("🔁 Proses item loan_item_id={$item['loan_item_id']}");

            $loanItem = LoanItem::findOrFail($item['loan_item_id']);
            $book = Books::findOrFail($item['book_id']);
            $returnedQty = $item['quantity'];

            if ($loanItem->quantity < $returnedQty) {
                Log::warning("⚠️ Jumlah return melebihi pinjaman: return={$returnedQty}, pinjam={$loanItem->quantity}");
                return response()->json([
                    'message' => "Jumlah pengembalian melebihi jumlah pinjaman."
                ], 422);
            }

            $loanItem->quantity -= $returnedQty;
            $loanItem->save();
            Log::info("✏️ LoanItem updated, sisa quantity: {$loanItem->quantity}");

            $book->increment('stock', $returnedQty);
            Log::info("📚 Stock buku ID {$book->id} ditambah: +$returnedQty");

            $return->returnItems()->create([
                'loan_item_id' => $item['loan_item_id'],
                'book_id' => $item['book_id'],
                'quantity' => $returnedQty,
                'condition_note' => $item['condition_note'] ?? null,
            ]);
            Log::info("📦 ReturnItem disimpan.");
        }

        $remaining = LoanItem::where('loan_id', $loan->id)
            ->where('quantity', '>', 0)
            ->exists();

        $loan->status = $remaining ? 'setengah' : 'selesai';
        $loan->save();
        Log::info("📌 Status loan ID {$loan->id} diubah ke: {$loan->status}");

        DB::commit();
        Log::info("✅ DB::commit() berhasil.");

        return response()->json(['message' => 'Detail pengembalian berhasil disimpan']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ DB::rollback() dijalankan karena error: " . $e->getMessage());

        return response()->json([
            'message' => 'Terjadi kesalahan saat menyimpan data',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
