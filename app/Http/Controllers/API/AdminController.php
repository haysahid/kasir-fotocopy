<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
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

        $countStores = Store::count();
        $countActiveStores = Store::where('activated_at', '!=', null)->where('disabled_at', null)->count();
        $countDisabledStores = Store::where('disabled_at', '!=', null)->count();
        $countStoreRequests = Store::where('activated_at', null)->count();

        return ResponseFormatter::success([
            'users' => $countUsers,
            'stores' => $countStores,
            'active_stores' => $countActiveStores,
            'disabled_stores' => $countDisabledStores,
            'store_requests' => $countStoreRequests,
        ], 'Data ringkasan berhasil diambil');
    }
}
