<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        $limit = $request->input('limit', 10);

        $employees = User::query();

        $employees->join('user_stores', 'users.id', '=', 'user_stores.user_id')
            ->where('user_stores.store_id', $store->id)
            ->where('users.role_id', 5)
            ->select('users.*');

        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Daftar karyawan berhasil ditemukan.',
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Check email unique
        if (User::where('email', $request->input('email'))->first()) {
            return ResponseFormatter::error('Email sudah terdaftar.', 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string',
            'password' => ['required', 'string', new Password(8)],
        ]);

        try {
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->password),
                'role_id' => 5,
            ]);

            $employee = User::where('email', $request->input('email'))->first();

            // Relation
            UserStore::create([
                'user_id' => $employee->id,
                'store_id' => $store->id,
            ]);

            return ResponseFormatter::success(
                $employee,
                'Akun karyawan berhasil dibuat',
                201
            );
        } catch (Exception $error) {
            return ResponseFormatter::error('Ada yang salah. Pembuatan akun karyawan gagal.' . $error, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Check user availability
        $employee = User::with(['store', 'role'])->where('role_id', 5)->find($id);

        if (!$employee || $employee->store[0]->id != $store->id) {
            return ResponseFormatter::error('Pengguna tidak ditemukan.', 404);
        }

        return ResponseFormatter::success(
            $employee,
            'Data pengguna ditemukan.',
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Check user availability
        $employee = User::with(['store', 'role'])->where('role_id', 5)->find($id);

        if (!$employee || $employee->store[0]->id != $store->id) {
            return ResponseFormatter::error('Pengguna tidak ditemukan.', 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'avatar' => 'nullable|file',
            'password' => ['nullable', 'string', new Password(8)],
        ]);

        if ($employee->email != $request->input('email')) {
            $request->validate([
                'email' => 'nullable|string|email|max:255|unique:users',
            ]);
        }

        try {
            $employee->update([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
            ]);

            if ($request->input('password')) {
                $employee->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            if ($request->hasFile('avatar')) {
                $avatar_path = '';

                // Delete old avatar
                if ($employee->avatar) {
                    Storage::delete($employee->avatar);
                }

                // Store avatar
                $avatar_path = $request->file('avatar')->store('user');

                // Add to database
                $employee->update([
                    'avatar' => $avatar_path,
                ]);
            }

            $employee = User::with(['store', 'role'])->find($employee->id);

            return ResponseFormatter::success($employee, 'Data pengguna berhasil diubah.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error("Ada yang salah. Autentikasi gagal. $error", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Check user availability
        $employee = User::with(['store', 'role'])->where('role_id', 5)->find($id);

        if (!$employee || $employee->store[0]->id != $store->id) {
            return ResponseFormatter::error('Pengguna tidak ditemukan.', 404);
        }

        // Delete avatar
        if ($employee->avatar) {
            Storage::delete($employee->avatar);
        }

        // Delete store relation
        UserStore::where('user_id', $employee->id)->delete();

        $employee->delete();

        return ResponseFormatter::success(
            $employee->id,
            'Pembelian berhasil dihapus.',
            200
        );
    }

    /**
     * Disable employee.
     */
    public function disable(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Check user availability
        $employee = User::with(['store', 'role'])->where('role_id', 5)->find($id);

        if (!$employee || $employee->store[0]->id != $store->id) {
            return ResponseFormatter::error('Pengguna tidak ditemukan.', 404);
        }

        $employee->update([
            'disabled_at' => now(),
        ]);

        return ResponseFormatter::success(
            $employee,
            'Karyawan berhasil dinonaktifkan.',
            200
        );
    }

    /**
     * Enable employee.
     */
    public function enable(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Check user availability
        $employee = User::with(['store', 'role'])->where('role_id', 5)->find($id);

        if (!$employee || $employee->store[0]->id != $store->id) {
            return ResponseFormatter::error('Pengguna tidak ditemukan.', 404);
        }

        $employee->update([
            'disabled_at' => null,
        ]);

        return ResponseFormatter::success(
            $employee,
            'Karyawan berhasil diaktifkan.',
            200
        );
    }
}
