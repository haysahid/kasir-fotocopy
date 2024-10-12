<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\User;
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
        $payment_status = $request->input('payment_status');
        $limit = $request->input('limit', 10);

        $user = Auth::user();
        $orders = SalesItem::query();

        if ($payment_status) {
            $orders->where('orders.payment_status', $payment_status);
        }

        $orders->where('user_id', $user->id)->with('order_items')->latest();

        return ResponseFormatter::success(
            $orders->paginate($limit),
            'Daftar penjualan berhasil ditemukan.',
            200
        );
    }

    /**
     * Sales a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user()->with['store'];
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        $request->validate([
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            // Create order
            $address = $request->input('address');
            $note = $request->input('note');
            $payment = $request->input('payment');

            $sales = Sale::create([
                'user_id' => $user->id,
                'address' => $address,
                'note' => $note,
                'payment' => $payment,
                'store_id' => $store->id,
            ]);

            $sales_items = $request->input('cart_items');

            foreach ($sales_items as $sales_item) {
                SalesItem::create([
                    'sales_id' => $sales->id,
                    'product_id' => $sales_item->product_id,
                    'quantity' => $sales_item->quantity,
                    'item_price' => $sales_item->product->selling_price,
                    'store_id' => $store->id,
                ]);
            }

            $sales = Sale::with(['sales_items'])->find($sales->id);

            return ResponseFormatter::success([
                'sales' => $sales,
                'return' => 0,
            ], 'Pesanan berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Pesanan gagal ditambahkan.' . $error, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sales = Sale::with('sales_items')->find($id);

        if (!$sales) {
            return ResponseFormatter::error('Pesanan tidak ditemukan.', 404);
        }

        return ResponseFormatter::success([
            'sales' => $sales,
        ], 'Pesanan berhasil ditemukan.', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sales = Sale::find($id);

        if (!$sales) {
            return ResponseFormatter::error('Pesanan tidak ditemukan.', 404);
        }

        $request->validate([]);

        try {
            $sales->update([]);

            $sales = Sale::with('sales_items')->find($id);

            return ResponseFormatter::success([
                'sales' => $sales,
            ], 'Pesanan berhasil diubah.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Pesanan gagal diubah.' . $error, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // $order = Sales::find($id);

        // if (!$order) {
        //     return ResponseFormatter::error('Pesanan tidak ditemukan.', 404);
        // }

        // $order->delete();

        // return ResponseFormatter::success(
        //     $order->id,
        //     'Pesanan berhasil dihapus.',
        //     200
        // );
    }
}
