<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Subscription;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $subscriptions = Subscription::query();

        $subscriptions->join('users as customer', 'customer.id', '=', 'subscriptions.user_id');

        if ($search) {
            $subscriptions->where('customer.name', 'like', "%$search%");
        }

        $subscriptions->with('customer');
        $subscriptions->select('subscriptions.*');
        $subscriptions->latest();

        return ResponseFormatter::success(
            $subscriptions->paginate($limit),
            'Data langganan berhasil ditemukan'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return ResponseFormatter::error('Data langganan tidak ditemukan', 404);
        }

        return ResponseFormatter::success($subscription, 'Data langganan berhasil ditemukan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return ResponseFormatter::error('Data langganan tidak ditemukan', 404);
        }

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'trial_periode_start' => 'nullable|date',
            'trial_periode_end' => 'nullable|date',
            'subscribe_after_trial' => 'nullable|boolean',
            'date_subscribed' => 'required|date',
            'valid_to' => 'required|date',
            'date_unsubscribed' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $subscription->update($validatedData);

            DB::commit();

            return ResponseFormatter::success($subscription, 'Data langganan berhasil diperbarui');
        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error('Data langganan gagal diperbarui', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return ResponseFormatter::error('Data langganan tidak ditemukan', 404);
        }

        try {
            DB::beginTransaction();

            $subscription->delete();

            DB::commit();

            return ResponseFormatter::success(null, 'Data langganan berhasil dihapus');
        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error('Data langganan gagal dihapus', 500);
        }
    }

    /**
     * Get customer subscription history.
     */
    public function history(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $customer = Auth::user();

        $subscriptions = Subscription::query();

        $subscriptions->join('users as customer', 'customer.id', '=', 'subscriptions.user_id')
            ->where('customer.id', $customer->id);

        if ($search) {
            $subscriptions->where('customer.name', 'like', "%$search%");
        }

        $subscriptions->with('customer');
        $subscriptions->select('subscriptions.*');
        $subscriptions->latest();

        return ResponseFormatter::success(
            $subscriptions->paginate($limit),
            'Data langganan berhasil ditemukan'
        );
    }
}
