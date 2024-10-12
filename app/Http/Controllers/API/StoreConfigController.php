<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreConfig;
use App\Models\User;
use App\Models\UserStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = StoreConfig::query()->get();

        $configs = null;

        foreach ($data as $config) {
            $configs[$config['key']] = $config['value'];
        }

        return ResponseFormatter::success(
            $configs,
            'Konfigurasi ditemukan.',
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Authorization check
        $user = Auth::user();
        $user = User::find($user->id);
        $store = User::with('store')->find($user->id)->store->first();
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && $user->role_id != 4)) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'required',
        ]);

        $key = $request->input('key');
        $value = $request->input('value');

        try {
            // Check if key is already exists
            $config = StoreConfig::where('key', $key)->first();

            if ($config) {
                return ResponseFormatter::error('Konfigurasi sudah ada.', 409);
            }

            $image_path = '';

            if ($request->hasFile('value')) {
                $image_path = $request->file('value')->store('public');
                $value = $image_path;
            }

            $config = StoreConfig::create([
                'key' => $key,
                'value' => $value,
                'store_id' => $store->id,
            ]);

            // Get all configs
            $data = StoreConfig::query()->where('store_id', $store->id)->get();

            $configs = [];

            foreach ($data as $config) {
                $configs[$config['key']] = $config['value'];
            }

            return ResponseFormatter::success(
                $configs,
                'Konfigurasi berhasil ditambahkan.',
                201
            );
        } catch (Exception $error) {
            return ResponseFormatter::error("Terjadi kesalahan. Konfigurasi gagal ditambahkan. $error", 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $setting = StoreConfig::find($id);

        if (!$setting) {
            return ResponseFormatter::error('Pengaturan tidak ditemukan.', 404);
        }

        return ResponseFormatter::success(
            [
                'setting' => $setting,
            ],
            'Pengaturan ditemukan.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Authorization check
        $user = Auth::user();
        $user = User::find($user->id);
        $store = User::with('store')->find($user->id)->store->first();
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && $user->role_id != 4)) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'required',
        ]);

        $key = $request->input('key');
        $value = $request->input('value');

        try {
            $image_path = '';

            if ($request->hasFile('value')) {
                $image_path = $request->file('value')->store('public');
                $value = $image_path;
            }

            $config = StoreConfig::where('key', $key)->where('store_id', $store->id)->update([
                'value' => $value,
            ]);

            // Get all configs
            $data = StoreConfig::query()->where('store_id', $store->id)->get();

            $configs = [];

            foreach ($data as $config) {
                $configs[$config['key']] = $config['value'];
            }

            return ResponseFormatter::success(
                $configs,
                'Konfigurasi berhasil diubah.',
                201
            );
        } catch (Exception $error) {
            return ResponseFormatter::error("Terjadi kesalahan. Konfigurasi gagal diubah. $error", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Authorization check
        $user = Auth::user();
        $user = User::find($user->id);
        $store = User::with('store')->find($user->id)->store->first();
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && $user->role_id != 4)) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        $config = StoreConfig::where('key', $request->input('key'))->first();

        if (!$config) {
            return ResponseFormatter::error('Konfigurasi tidak ditemukan.', 404);
        }

        $config->delete();

        return ResponseFormatter::success(
            [
                'key' => $config->key,
            ],
            'Konfigurasi berhasil dihapus.',
            200
        );
    }
}
