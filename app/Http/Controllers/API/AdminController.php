<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    // Get summary
    public function summary()
    {
        $countUsers = User::count();

        $countSubscribedUsers = User::get()->filter(function ($user) {
            return $user->has_active_subscription;
        })->count();

        $countStores = Store::count();

        $countIncome = Invoice::where('paid_at', '!=', null)->sum('amount');


        $countMeanMonthlyIncome = Invoice::where('paid_at', '!=', null)
            ->where('paid_at', '>=', now()->subMonth())
            ->sum('amount');

        return ResponseFormatter::success([
            'users' => $countUsers,
            'subscribed_users' => $countSubscribedUsers,
            'stores' => $countStores,
            'total_income' => $countIncome,
            'mean_monthly_income' => $countMeanMonthlyIncome,
        ], 'Data ringkasan berhasil diambil');
    }
}
