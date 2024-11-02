<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreConfig;
use App\Models\User;
use App\Models\UserStore;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2, 3];

        if (!in_array($user->role_id, $allowedRoles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }


        $search = $request->input('search');
        $limit = $request->input('limit', 10);

        $stores = Store::query();

        if ($search) {
            $stores
                ->where('stores.name', 'like', '%' . $search . '%')
                ->orWhere('stores.description', 'like', '%' . $search . '%');
        }

        $stores->with(['users', 'owners'])->select('stores.*')->latest();

        return ResponseFormatter::success(
            $stores->paginate($limit),
            'Daftar toko berhasil ditemukan.',
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|file',
            'banner' => 'nullable|file',
        ]);

        $user = Auth::user();

        try {
            $user = User::with('store')->find($user->id);

            // Prevent admin from creating store
            if ($user->role_id != 6) {
                return ResponseFormatter::error('Anda tidak boleh membuat toko.', 401);
            }

            // Prevent multiple store
            if ($user->store && $user->store->count() > 0) {
                return ResponseFormatter::error('Anda sudah memiliki toko.', 400);
            }

            $store = Store::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'address' => $request->input('address'),
                'community_id' => 1,
            ]);

            // Logo
            if ($request->hasFile('logo')) {
                $logo = null;
                $logo = $request->file('logo')->store('store/' . $store->id);

                $store->update([
                    'logo' => $logo,
                ]);
            }

            // Banner
            if ($request->hasFile('banner')) {
                $banner = null;
                $banner = $request->file('banner')->store('store/' . $store->id);

                $store->update([
                    'banner' => $banner,
                ]);
            }

            $store = Store::find($store->id);


            // Relation
            UserStore::create([
                'user_id' => $user->id,
                'store_id' => $store->id,
            ]);

            return ResponseFormatter::success([
                'store' => $store,
            ], 'Toko berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Toko gagal ditambahkan.' . $error, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $store = Store::with(['users'])->find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && !$isOwner) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        return ResponseFormatter::success([
            'store' => $store,
        ], 'Toko berhasil ditemukan.', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|file',
            'banner' => 'nullable|file',
        ]);

        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && $user->role_id != 4)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        try {
            $store->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'address' => $request->input('address'),
            ]);

            // Logo
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($store->logo) {
                    Storage::delete($store->logo);
                }

                $logo = $request->file('logo')->store('store/' . $store->id);

                $store->update([
                    'logo' => $logo,
                ]);
            }

            // Banner
            if ($request->hasFile('banner')) {
                // Delete old banner
                if ($store->banner) {
                    Storage::delete($store->banner);
                }

                $banner = $request->file('banner')->store('store/' . $store->id);

                $store->update([
                    'banner' => $banner,
                ]);
            }

            $store = Store::with('users')->find($store->id);

            return ResponseFormatter::success([
                'store' => $store,
            ], 'Data toko berhasil diubah.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Toko gagal diubah.' . $error, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2];

        if (!in_array($user->role_id, $allowedRoles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        // Delete relation
        UserStore::where('store_id', $store->id)->delete();

        // Delete store
        $store->delete();

        // Update user role
        $users = UserStore::where('store_id', $store->id)->get();
        $ownerId = User::where('role_id', 4)->first()->id;

        User::find($ownerId)->update([
            'role_id' => 6,
        ]);

        return ResponseFormatter::success(
            $store->id,
            'Toko berhasil dihapus.',
            200
        );
    }

    /**
     * Activate store.
     */
    public function activate(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2];

        if (!in_array($user->role_id, $allowedRoles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        if ($store->activated_at) {
            return ResponseFormatter::error('Toko sudah diaktifkan.', 400);
        }

        try {
            $store->update([
                'activated_at' => now(),
            ]);

            $ownerId = UserStore::where('store_id', $store->id)->first()->user_id;
            User::find($ownerId)->update([
                'role_id' => 4,
            ]);

            $store = Store::with('users')->find($store->id);

            return ResponseFormatter::success([
                'store' => $store,
            ], 'Toko berhasil diaktifkan.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Toko gagal diaktifkan.' . $error, 500);
        }
    }

    /**
     * Reject store.
     */
    public function reject(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2];

        if (!in_array($user->role_id, $allowedRoles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        try {
            UserStore::where('store_id', $store->id)->delete();
            StoreConfig::where('store_id', $store->id)->delete();

            $store->forceDelete();

            return ResponseFormatter::success([
                'store' => $store,
            ], 'Toko berhasil ditolak.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Toko gagal ditolak.' . $error, 500);
        }
    }

    /**
     * Disable store.
     */
    public function disable(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2];

        if (!in_array($user->role_id, $allowedRoles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        try {
            $store->update([
                'disabled_at' => now(),
            ]);

            $store = Store::with('users')->find($store->id);

            return ResponseFormatter::success([
                'store' => $store,
            ], 'Toko berhasil diaktifkan.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Toko gagal diaktifkan.' . $error, 500);
        }
    }

    /**
     * Disable store.
     */
    public function enable(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2];

        if (!in_array($user->role_id, $allowedRoles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        try {
            $store->update([
                'disabled_at' => null,
            ]);

            $store = Store::with('users')->find($store->id);

            return ResponseFormatter::success([
                'store' => $store,
            ], 'Toko berhasil diaktifkan.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Toko gagal diaktifkan.' . $error, 500);
        }
    }

    // Store Summary
    public function summary(Request $request, string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && !$isOwner) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Validate date
        try {
            new DateTime($startDate);
            new DateTime($endDate);
        } catch (Exception $error) {
            return ResponseFormatter::error('Format tanggal tidak valid.', 400);
        }

        // Prevent similar date
        if ($startDate > $endDate) {
            return ResponseFormatter::error('Tanggal tidak valid.', 400);
        }

        $summary = $store->summary($startDate, $endDate . ' 23:59:59');

        return ResponseFormatter::success(
            $summary,
            'Ringkasan toko berhasil ditemukan.',
            200
        );
    }

    // Store Graph
    public function graph(Request $request, string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && !$isOwner) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        // Type: daily, weekly, monthly
        $type = $request->input('type');
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $week = $request->input('week', Carbon::now()->month);

        if (!in_array($type, ['daily', 'weekly', 'monthly'])) {
            return ResponseFormatter::error('Tipe grafik tidak valid.', 400);
        }

        if ($type == 'daily') {
            return ResponseFormatter::success(
                $store->dailySalesRevenueGraph($year, $month, $week),
                'Grafik harian berhasil ditemukan.',
                200
            );
        } elseif ($type == 'weekly') {
            return ResponseFormatter::success(
                $store->weeklyMonthlySalesRevenueGraph($year, $month),
                'Grafik mingguan berhasil ditemukan.',
                200
            );
        } elseif ($type == 'monthly') {
            return ResponseFormatter::success(
                $store->monthlySalesRevenueGraph($year),
                'Grafik bulanan berhasil ditemukan.',
                200
            );
        }
    }

    // Store Check Products
    public function lowStockProduct(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ResponseFormatter::error('Toko tidak ditemukan.', 404);
        }

        // Authorization check
        $user = Auth::user();
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && !$isOwner) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        return ResponseFormatter::success(
            $store->lowStockProduct(),
            'Daftar produk berhasil ditemukan.',
            200
        );
    }
}
