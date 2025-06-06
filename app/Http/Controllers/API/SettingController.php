<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Setting::query()->get();

        $settings = [];

        foreach ($data as $setting) {
            $settings[$setting['key']] = $setting['value'];
        }

        return ResponseFormatter::success(
            $settings,
            'Pengaturan ditemukan.',
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
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

            $setting = Setting::create([
                'key' => $key,
                'value' => $value,
            ]);

            // Get all settings
            $data = Setting::query()->get();

            $settings = [];

            foreach ($data as $setting) {
                $settings[$setting['key']] = $setting['value'];
            }

            return ResponseFormatter::success(
                [
                    'settings' => $settings,
                ],
                'Pengaturan berhasil ditambahkan.',
                201
            );
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Pengaturan gagal ditambahkan.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $setting = Setting::find($id);

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
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $key = $request->input('key');
        $value = $request->input('value');

        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return ResponseFormatter::error('Pengaturan tidak ditemukan.', 404);
        }

        try {
            if ($request->hasFile('value') && $key == 'app_logo') {
                $image_path = '';

                // Delete old image
                if ($setting->value) {
                    Storage::delete($setting->value);
                }

                // Store image
                $file_name = $request->file('value')->getClientOriginalName();
                $image_path = $request->file('value')->storeAs('public', $file_name);
                $value = $image_path;
            }

            Setting::where('key', $key)->update([
                'value' => $value,
            ]);

            // Get all settings
            $data = Setting::query()->get();

            $settings = [];

            foreach ($data as $setting) {
                $settings[$setting['key']] = $setting['value'];
            }

            return ResponseFormatter::success(
                [
                    'settings' => $settings,
                ],
                'Pengaturan berhasil diubah.',
                200,
            );
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Pengaturan gagal diubah.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $setting = Setting::where('key', $request->input('key'))->first();

        if (!$setting) {
            return ResponseFormatter::error('Pengaturan tidak ditemukan.', 404);
        }

        $setting->delete();

        return ResponseFormatter::success(
            [
                'key' => $setting->key,
            ],
            'Pengaturan berhasil dihapus.',
            200
        );
    }
}
