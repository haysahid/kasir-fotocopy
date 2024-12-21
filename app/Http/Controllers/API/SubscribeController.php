<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
    public function createSnapToken(Request $request)
    {
        $validatedData = $request->validate([
            'plan_id' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $plan = Plan::find($validatedData['plan_id']);
        $quantity = $validatedData['quantity'];

        if (!$plan) {
            return ResponseFormatter::error('Plan not found', 404);
        }

        $grossAmount = $plan->price * $quantity;

        $customer = Auth::user();

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
            'params' => $params,
        ], 'Snap token generated successfully');
    }
}
