<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StoreConfig;
use App\Models\User;
use App\Models\UserStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        $limit = $request->input('limit', 10);

        $purchases = Purchase::query();

        $purchases->where('store_id', $store->id)->with(['purchase_items'])->latest();

        return ResponseFormatter::success(
            $purchases->paginate($limit),
            'Daftar pembelian berhasil ditemukan.',
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        $request->validate([
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
            'payment' => 'required|integer',
        ]);

        try {

            $address = $request->input('address');
            $note = $request->input('note');
            $payment = $request->input('payment');
            $purchase_items = $request->input('purchase_items');

            // Count total
            $total = 0;

            foreach ($purchase_items as $purchase_item) {
                $item = new PurchaseItem($purchase_item);
                $total += $item->quantity * $item->item_price;
            }

            // Check payment
            if ($payment < $total) {
                return ResponseFormatter::error('Pembayaran tidak mencukupi.', 400);
            }

            // Create order
            $storeAcronym = StoreConfig::where('key', 'store_acronym')->where('store_id', $store->id)->first();
            $code = $storeAcronym->value . '-B-' . date('YmdHis');

            $purchase = Purchase::create([
                'code' => $code,
                'user_id' => $user->id,
                'address' => $address,
                'note' => $note,
                'payment' => $payment,
                'store_id' => $store->id,
            ]);

            // Create order items
            foreach ($purchase_items as $purchase_item) {
                $item = new PurchaseItem($purchase_item);
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'item_price' => $item->item_price,
                    'store_id' => $store->id,
                ]);
            }

            $purchase = Purchase::with(['purchase_items'])->find($purchase->id);

            return ResponseFormatter::success($purchase, 'Pembelian berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Pembelian gagal ditambahkan.' . $error, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check purchase availability
        $purchase = Purchase::with('purchase_items')->find($id);

        if (!$purchase) {
            return ResponseFormatter::error('Pembelian tidak ditemukan.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        return ResponseFormatter::success($purchase, 'Pembelian berhasil ditemukan.', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check purchase availability
        $purchase = Purchase::with('purchase_items')->find($id);

        if (!$purchase) {
            return ResponseFormatter::error('Pembelian tidak ditemukan.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        $request->validate([
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
            'payment' => 'required|integer',
        ]);

        try {

            $address = $request->input('address');
            $note = $request->input('note');
            $payment = $request->input('payment');
            $purchase_items = $request->input('purchase_items');

            // Count total
            $total = 0;

            foreach ($purchase_items as $purchase_item) {
                $item = new PurchaseItem($purchase_item);
                $total += $item->quantity * $item->item_price;
            }

            // Check payment
            if ($payment < $total) {
                return ResponseFormatter::error('Pembayaran tidak mencukupi.', 400);
            }

            // Update order
            $purchase->update([
                'user_id' => $user->id,
                'address' => $address,
                'note' => $note,
                'payment' => $payment,
            ]);

            // Update order items
            foreach ($purchase_items as $purchase_item) {
                $item = new PurchaseItem($purchase_item);
                $item->updateOrCreate([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'item_price' => $item->item_price,
                    'store_id' => $store->id,
                ]);
            }

            // Delete order items
            foreach ($purchase->purchase_items as $purchase_item) {
                $found = false;

                foreach ($purchase_items as $item) {
                    if ($purchase_item->product_id == $item['product_id']) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $purchase_item->delete();
                }
            }

            $purchase = Purchase::with(['purchase_items'])->find($purchase->id);

            return ResponseFormatter::success($purchase, 'Pembelian berhasil diubah.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Pembelian gagal diubah.' . $error, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check purchase availability
        $purchase = Purchase::with('purchase_items')->find($id);

        if (!$purchase) {
            return ResponseFormatter::error('Pembelian tidak ditemukan.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        // Delete order items
        foreach ($purchase->purchase_items as $purchase_item) {
            $purchase_item->delete();
        }

        $purchase->delete();

        return ResponseFormatter::success(
            $purchase->id,
            'Pembelian berhasil dihapus.',
            200
        );
    }
}
