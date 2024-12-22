<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_id' => 'required|integer',
            'payment_method_id' => 'required|integer',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:255',
        ]);

        $invoiceId = $validatedData['invoice_id'];
        $paymentMethodId = $validatedData['payment_method_id'];
        $amount = $validatedData['amount'];
        $note = $validatedData['note'];

        // Find invoice
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            return ResponseFormatter::error('Tagihan tidak ditemukan', 404);
        }

        // Find payment method
        $paymentMethod = PaymentMethod::find($paymentMethodId);

        if (!$paymentMethod) {
            return ResponseFormatter::error('Metode pembayaran tidak ditemukan', 404);
        }

        // Check if invoice is already paid
        if ($invoice->paid_at) {
            return ResponseFormatter::error('Tagihan sudah dibayar', 400);
        }

        // Check if invoice is expired
        if ($invoice->due_at < now()) {
            return ResponseFormatter::error('Tagihan sudah kadaluarsa', 400);
        }

        // Check if payment amount is less than invoice amount
        if ($amount < $invoice->amount) {
            return ResponseFormatter::error('Jumlah pembayaran kurang dari tagihan', 400);
        }

        // Create invoice payment
        $invoicePayment = $invoice->invoicePayments()->create([
            'payment_method_id' => $paymentMethodId,
            'amount' => $amount,
            'is_valid' => true,
            'note' => $note,
        ]);

        // Update invoice paid_at
        $invoice->update([
            'paid_at' => now(),
        ]);

        $invoice->load('subscription.customer', 'planHistory.plan');

        return ResponseFormatter::success([
            'invoice' => $invoice,
            'invoice_payment' => $invoicePayment,
        ], 'Pembayaran tagihan berhasil');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
