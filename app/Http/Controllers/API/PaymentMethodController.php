<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->input('limit', 10);
        $search = request()->input('search');

        $paymentMethods = PaymentMethod::query();

        if ($search) {
            $paymentMethods->where('name', 'like', "%$search%");
        }

        $paymentMethods->latest();

        return ResponseFormatter::success(
            $paymentMethods->paginate($limit),
            'Data metode pembayaran berhasil ditemukan'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $paymentMethod = PaymentMethod::create($validatedData);

            return ResponseFormatter::success($paymentMethod, 'Data metode pembayaran berhasil ditambahkan');
        } catch (Exception $error) {
            return ResponseFormatter::error('Data metode pembayaran gagal ditambahkan', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return ResponseFormatter::error('Data metode pembayaran tidak ditemukan', 404);
        }

        return ResponseFormatter::success($paymentMethod, 'Data metode pembayaran berhasil ditemukan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return ResponseFormatter::error('Data metode pembayaran tidak ditemukan', 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $paymentMethod->update($validatedData);

            return ResponseFormatter::success($paymentMethod, 'Data metode pembayaran berhasil diperbarui');
        } catch (Exception $error) {
            return ResponseFormatter::error('Data metode pembayaran gagal diperbarui', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return ResponseFormatter::error('Data metode pembayaran tidak ditemukan', 404);
        }

        try {
            $paymentMethod->delete();

            return ResponseFormatter::success(null, 'Data metode pembayaran berhasil dihapus');
        } catch (Exception $error) {
            return ResponseFormatter::error('Data metode pembayaran gagal dihapus', 500);
        }
    }

    /**
     * Get payment method dropdown.
     */
    public function dropdown(Request $request)
    {
        $limit = $request->input('limit');
        $search = $request->input('search');

        $paymentMethod = PaymentMethod::query();

        if ($search) {
            $paymentMethod->where('name', 'like', "%$search%");
        }

        if ($limit) {
            $paymentMethod->limit($limit);
        }

        $paymentMethod = $paymentMethod->latest()->get();

        return ResponseFormatter::success($paymentMethod, 'Data metode pembayaran berhasil ditemukan');
    }
}
