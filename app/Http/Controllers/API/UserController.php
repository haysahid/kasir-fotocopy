<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Rate limiter
        $rateLimiter = RateLimiter::attempt(
            'register:' . $request->ip(),
            $perMinute = 10,
            function () {}
        );

        if (!$rateLimiter) {
            return ResponseFormatter::error('Terlalu banyak percobaan register. Coba lagi nanti.', 429);
        }

        // Check email unique
        if (User::where('email', $request->input('email'))->first()) {
            return ResponseFormatter::error('Email sudah terdaftar.', 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string',
            'password' => [
                'required',
                'string',
                new Password(8),
                'regex:/^(?!.*\s)(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\W_]).*$/'
            ]
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf, angka, dan karakter spesial.',
        ]);

        try {
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->password),
                'role_id' => 6,
            ]);

            $user = User::where('email', $request->input('email'))->first();
            $token = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Akun berhasil dibuat', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Ada yang salah. Autentikasi gagal.' . $error, 500);
        }
    }

    public function login(Request $request)
    {
        // Rate limiter
        $rateLimiter = RateLimiter::attempt(
            'login:' . $request->input('email') ?: $request->ip(),
            $perMinute = 20,
            function () {}
        );

        if (!$rateLimiter) {
            return ResponseFormatter::error('Terlalu banyak percobaan login. Coba lagi nanti.', 429);
        }

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        try {
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error('Email atau password salah. Autentikasi gagal.', 401);
            }

            $user = User::where('email', $request->input('email'))->with(['store', 'role'])->first();

            if (!Hash::check($request->input('password'), $user->password, [])) {
                throw new Exception('Invalid credentials');
            }

            if ($user->disabled_at) {
                return ResponseFormatter::error('Status pengguna tidak aktif. Hubungi Admin apabila ada kesalahan.', 400);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Berhasil masuk', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Ada yang Salah. Autentikasi gagal.'  . $error, 500);
        }
    }

    public function fetch()
    {
        $user = User::with(['store', 'role'])->find(Auth::user()->id);

        return ResponseFormatter::success([
            'user' => $user,
        ], 'Data pengguna ditemukan.', 200);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp',
        ], [
            'avatar.uploaded' => 'Avatar harus berupa file gambar.',
        ]);

        $user = user::query()->find(Auth::user()->id);

        if ($user->email != $request->input('email')) {
            $request->validate([
                'email' => 'nullable|string|email|max:255|unique:users',
            ]);
        }

        try {
            DB::beginTransaction();

            $user->update($request->all());

            if ($request->hasFile('avatar')) {
                $avatar_path = '';

                // Delete old avatar
                if ($user->avatar) {
                    Storage::delete($user->avatar);
                }

                // Store avatar
                $avatar_path = $request->file('avatar')->store('user');

                // Add to database
                $user->update([
                    'avatar' => $avatar_path,
                ]);
            }

            DB::commit();

            $user = User::with(['store', 'role'])->find($user->id);

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Data pengguna berhasil diubah.', 200);
        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error('Ada yang salah. Autentikasi gagal.', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success([
            'token' => $token,
        ], 'Berhasil keluar.', 200);
    }

    // Get all users
    public function index(Request $request)
    {
        $showDisabled = $request->input('show_disabled', true);
        $search = $request->input('search');
        $limit = $request->input('limit', 10);

        $roleId = $request->input('role_id');

        $users = User::query();

        if (!$showDisabled) {
            $users->whereNull('disabled_at');
        }

        if ($roleId) {
            $users->where('role_id', $roleId);
        }

        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users->with(['store', 'role'])->latest()->get();

        return ResponseFormatter::success($users->paginate($limit), 'Data pengguna ditemukan.', 200);
    }

    // Create new user
    public function store(Request $request)
    {
        // Check email unique
        if (User::where('email', $request->input('email'))->first()) {
            return ResponseFormatter::error('Email sudah terdaftar.', 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp',
            'role_id' => 'required|integer',
            'password' => [
                'required',
                'string',
                new Password(8),
                'regex:/^(?!.*\s)(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\W_]).*$/'
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'role_id.required' => 'Role wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf, angka, dan karakter spesial.',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'role_id' => $request->input('role_id'),
                'password' => Hash::make($request->password),
            ]);

            if ($request->hasFile('avatar')) {
                $avatar_path = '';

                // Store avatar
                $avatar_path = $request->file('avatar')->store('user');

                // Add to database
                $user->update([
                    'avatar' => $avatar_path,
                ]);
            }

            DB::commit();

            $user = User::with(['store', 'role'])->find($user->id);

            return ResponseFormatter::success($user, 'Data pengguna berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error('Ada yang salah. Autentikasi gagal.', 500);
        }
    }

    // Get user by id
    public function show($id)
    {
        $user = User::with(['store', 'role'])->find($id);

        return ResponseFormatter::success($user, 'Data pengguna ditemukan.', 200);
    }

    // Update user by id
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'password' => [
                'nullable',
                'string',
                new Password(8),
                'regex:/^(?!.*\s)(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\W_]).*$/'
            ],
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp',
            'role_id' => 'nullable|integer',
            'disabled_at' => 'nullable|date',
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf, angka, dan karakter spesial.',
            'avatar.uploaded' => 'Avatar harus berupa file gambar.',
        ]);

        $user = User::query()->find($id);

        if ($user->email != $request->input('email')) {
            $request->validate([
                'email' => 'nullable|string|email|max:255|unique:users',
            ]);
        }

        try {
            DB::beginTransaction();

            $user->update($request->all());

            if ($request->hasFile('avatar')) {
                $avatar_path = '';

                // Delete old avatar
                if ($user->avatar) {
                    Storage::delete($user->avatar);
                }

                // Store avatar
                $avatar_path = $request->file('avatar')->store('user');

                // Add to database
                $user->update([
                    'avatar' => $avatar_path,
                ]);
            }

            DB::commit();

            $user = User::with(['store', 'role'])->find($user->id);

            return ResponseFormatter::success($user, 'Data pengguna berhasil diubah.', 200);
        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error('Ada yang salah. Autentikasi gagal.', 500);
        }
    }

    // Delete user by id
    public function destroy($id)
    {
        $user = User::find($id);

        // Check user availability
        if (!$user) {
            return ResponseFormatter::error('Data pengguna tidak ditemukan.', 404);
        }

        // Prevent superadmin, admin, and community to delete their own account
        if ($user->role_id == 1 || $user->role_id == 2 || $user->role_id == 3) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        DB::beginTransaction();

        // Delete user avatar
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        $user->delete();

        DB::commit();

        return ResponseFormatter::success(null, 'Data pengguna berhasil dihapus.', 200);
    }

    // Disable user by id
    public function disable($id)
    {
        $user = User::find($id);

        // Check user availability
        if (!$user) {
            return ResponseFormatter::error('Data pengguna tidak ditemukan.', 404);
        }

        // Prevent superadmin, admin, and community to disable their own account
        if ($user->role_id == 1 || $user->role_id == 2 || $user->role_id == 3) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        $user->update([
            'disabled_at' => now(),
        ]);

        return ResponseFormatter::success(null, 'Data pengguna berhasil dinonaktifkan.', 200);
    }

    // Enable user by id
    public function enable($id)
    {
        $user = User::find($id);

        // Check user availability
        if (!$user) {
            return ResponseFormatter::error('Data pengguna tidak ditemukan.', 404);
        }

        $user->update([
            'disabled_at' => null,
        ]);

        return ResponseFormatter::success(null, 'Data pengguna berhasil diaktifkan.', 200);
    }
}
