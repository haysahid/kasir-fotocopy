<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\StoreConfig;
use App\Models\User;
use App\Models\UserStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
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

        $sales = Sale::query();

        $sales->where('store_id', $store->id)->where('type', 'sales')->with(['sales_items'])->latest();

        return ResponseFormatter::success(
            $sales->paginate($limit),
            'Daftar penjualan berhasil ditemukan.',
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
            'address' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'payment' => 'required|integer',
        ]);

        try {

            $address = $request->input('address');
            $note = $request->input('note');
            $payment = $request->input('payment');
            $sales_items = $request->input('sales_items');

            // Count total
            $total = 0;

            foreach ($sales_items as $sales_item) {
                $item = new SalesItem($sales_item);
                $total += $item->quantity * $item->item_price;
            }

            // Check payment
            if ($payment < $total) {
                return ResponseFormatter::error('Pembayaran tidak mencukupi.', 400);
            }

            // Create order
            $storeAcronym = StoreConfig::where('key', 'store_acronym')->where('store_id', $store->id)->first();
            $code = $storeAcronym->value . '-J-' . date('YmdHis');

            $sale = Sale::create([
                'code' => $code,
                'user_id' => $user->id,
                'address' => $address,
                'note' => $note,
                'payment' => $payment,
                'store_id' => $store->id,
            ]);

            // Create order items
            foreach ($sales_items as $sales_item) {
                $item = new SalesItem($sales_item);
                SalesItem::create([
                    'sales_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'item_price' => $item->item_price,
                    'store_id' => $store->id,
                ]);
            }

            $sale = Sale::with(['sales_items'])->find($sale->id);

            return ResponseFormatter::success($sale, 'Penjualan berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Penjualan gagal ditambahkan.' . $error, 500);
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

        // Check sale availability
        $sale = Sale::with('sales_items')->find($id);

        if (!$sale) {
            return ResponseFormatter::error('Penjualan tidak ditemukan.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        return ResponseFormatter::success($sale, 'Penjualan berhasil ditemukan.', 200);
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

        // Check sale availability
        $sale = Sale::with('sales_items')->find($id);

        if (!$sale) {
            return ResponseFormatter::error('Penjualan tidak ditemukan.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        $request->validate([
            'address' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'payment' => 'required|integer',
        ]);

        try {

            $address = $request->input('address');
            $note = $request->input('note');
            $payment = $request->input('payment');
            $sales_items = $request->input('sales_items');

            // Count total
            $total = 0;

            foreach ($sales_items as $sales_item) {
                $item = new SalesItem($sales_item);
                $total += $item->quantity * $item->item_price;
            }

            // Check payment
            if ($payment < $total) {
                return ResponseFormatter::error('Pembayaran tidak mencukupi.', 400);
            }

            // Update order
            $sale->update([
                'user_id' => $user->id,
                'address' => $address,
                'note' => $note,
                'payment' => $payment,
            ]);

            // Update order items
            foreach ($sales_items as $sales_item) {
                $item = new SalesItem($sales_item);
                $item->updateOrCreate([
                    'sales_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'item_price' => $item->item_price,
                    'store_id' => $store->id,
                ]);
            }

            // Delete order items
            foreach ($sale->sales_items as $sales_item) {
                $found = false;

                foreach ($sales_items as $item) {
                    if ($sales_item->product_id == $item['product_id']) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $sales_item->delete();
                }
            }

            $sale = Sale::with(['sales_items'])->find($sale->id);

            return ResponseFormatter::success($sale, 'Penjualan berhasil diubah.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Penjualan gagal diubah.' . $error, 500);
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

        // Check sale availability
        $sale = Sale::with('sales_items')->find($id);

        if (!$sale) {
            return ResponseFormatter::error('Penjualan tidak ditemukan.', 404);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        // Delete order items
        foreach ($sale->sales_items as $sales_item) {
            $sales_item->delete();
        }

        $sale->delete();

        return ResponseFormatter::success(
            $sale->id,
            'Penjualan berhasil dihapus.',
            200
        );
    }

    // Add expired product
    public function addExpiredProduct(Request $request)
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

        try {
            $note = "Produk kadaluarsa";
            $payment = 0;
            $sales_items = $request->input('sales_items');

            // Create order
            $storeAcronym = StoreConfig::where('key', 'store_acronym')->where('store_id', $store->id)->first();
            $code = $storeAcronym->value . '-E-' . date('YmdHis');

            $sale = Sale::create([
                'code' => $code,
                'user_id' => $user->id,
                'note' => $note,
                'payment' => $payment,
                'type' => 'expired',
                'store_id' => $store->id,
            ]);

            // Create order items
            foreach ($sales_items as $sales_item) {
                $item = new SalesItem($sales_item);
                SalesItem::create([
                    'sales_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'item_price' => 0,
                    'store_id' => $store->id,
                ]);
            }

            $sale = Sale::with(['sales_items'])->find($sale->id);

            return ResponseFormatter::success($sale, 'Produk kadaluarsa berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Produk kadaluarsa gagal ditambahkan.' . $error, 500);
        }
    }
}
