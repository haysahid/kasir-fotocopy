<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
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
            'plan_id' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $plan = Plan::find($validatedData['plan_id']);
        $quantity = $validatedData['quantity'];

        if (!$plan) {
            return ResponseFormatter::error('Plan not found', 404);
        }

        $grossAmount = $plan->price * $quantity * 1.12;

        $customer = Auth::user();
        $customer = User::find($customer->id);

        // Subscription data
        try {
            DB::beginTransaction();

            $dateSubscribed = now();

            if ($plan->duration_type == 'days') {
                $validTo = now()->addDays($plan->duration);
            } elseif ($plan->duration_type == 'weeks') {
                $validTo = now()->addWeeks($plan->duration);
            } elseif ($plan->duration_type == 'months') {
                $validTo = now()->addMonths($plan->duration);
            } elseif ($plan->duration_type == 'years') {
                $validTo = now()->addYears($plan->duration);
            }

            if ($plan->price == 0) {
                $trialPeriodeStart = $dateSubscribed;
                $trialPeriodeEnd = $validTo;
                $subscribeAfterTrial = true;
            } else {
                $trialPeriodeStart = null;
                $trialPeriodeEnd = null;
                $subscribeAfterTrial = false;
            }

            // Create subscription
            $subscription = $customer->subscriptions()->create([
                'trial_periode_start' => $trialPeriodeStart,
                'trial_periode_end' => $trialPeriodeEnd,
                'subscribe_after_trial' => $subscribeAfterTrial,
                'date_subscribed' => $dateSubscribed,
                'valid_to' => $validTo,
            ]);

            // Create plan history
            $planHistory = $subscription->planHistories()->create([
                'plan_id' => $plan->id,
                'quantity' => $quantity,
                'date_start' => $dateSubscribed,
                'date_end' => $validTo,
            ]);

            // Create invoice
            $invoice = $subscription->invoices()->create([
                'plan_history_id' => $planHistory->id,
                'description' => 'Silahkan melakukan pembayaran untuk berlangganan ' . $plan->name,
                'amount' => $grossAmount,
                'due_at' => $dateSubscribed->addDays(1),
            ]);

            DB::commit();

            return ResponseFormatter::success($invoice, 'Invoice created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return ResponseFormatter::error('Invoice not found', 404);
        }

        $invoice->load('subscription.customer', 'planHistory.plan', 'invoicePayments');

        return ResponseFormatter::success($invoice, 'Invoice retrieved successfully');
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
