<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscribeController extends Controller
{
    public function createSnapToken(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_id' => 'required|integer',
        ]);

        $invvoiceId = $validatedData['invoice_id'];
        $invoice = Invoice::find($invvoiceId);

        if (!$invoice) {
            return ResponseFormatter::error('Invoice not found', 404);
        }

        $invoice->load('subscription.customer', 'planHistory.plan');

        $plan = $invoice->planHistory->plan;
        $quantity = $invoice->planHistory->quantity;
        $grossAmount = $invoice->amount;
        $customer = $invoice->subscription->customer;

        // Midtrans

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = false;

        $params = [
            'item_details' => [
                [
                    'id' => $plan->id,
                    'price' => $plan->price,
                    'quantity' => $quantity,
                    'name' => $plan->name,
                ],
                [
                    'id' => 'PPN',
                    'price' => $plan->price * $quantity * 0.11,
                    'quantity' => 1,
                    'name' => 'PPN (11%)',
                ]
            ],
            'transaction_details' => [
                'order_id' => rand(),
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customer->name,
                'last_name' => '',
                'email' => $customer->email,
                'phone' => $customer->phone,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return ResponseFormatter::success([
            'snap_token' => $snapToken,
            'invoice' => $invoice,
        ], 'Snap token generated successfully');
    }

    public function setPayment(Request $request) {}
}
