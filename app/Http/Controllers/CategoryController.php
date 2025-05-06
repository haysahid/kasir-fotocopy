<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Category;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by');
        $sortOrder = $request->input('sort_order', 'asc');
        $storeId = $request->input('store_id');

        $user = Auth::user();
        $user = User::find($user->id);
        $store = $user->store()->first();

        $categories = Category::query();

        if ($user->role_id > 3) {
            if (!$store) {
                return ResponseFormatter::error('Anda belum memiliki toko', 404);
            }

            $categories->where('store_id', $store->id);
        } elseif ($user->role_id == 3) {
            $store = Store::where('is_community', 1)->first();

            if (!$store) {
                return ResponseFormatter::error('Anda belum memiliki toko', 404);
            }

            $categories->where('store_id', $store->id);
        }

        if ($storeId) {
            $categories->where('store_id', $storeId);
        }

        if ($search) {
            $categories->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($sortBy) {
            $categories->orderBy($sortBy, $sortOrder);
        }

        $categories->latest();

        return ResponseFormatter::success(
            $categories->paginate($limit),
            'Daftar kategori berhasil ditemukan',
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);

        // Check if user is community
        if ($user->role_id == 3) {
            $store = Store::where('is_community', 1)->first();
        } else {
            $store = $user->store->first();
        }

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check store activation
        if (!$store->activated_at) {
            return ResponseFormatter::error('Toko belum diaktifkan.', 403);
        }

        // Authorization check
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        $isAllowedRole = in_array($user->role_id, $allowedRoles);
        $isSpecialRole = in_array($user->role_id, [4, 5]);
        $isAuthorized = $isOwner || $isSpecialRole;

        if (!$isAllowedRole && !$isAuthorized) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'name.required' => 'Nama kategori tidak boleh kosong',
            'name.string' => 'Nama kategori harus berupa string',
            'name.max' => 'Nama kategori maksimal 255 karakter',
            'description.string' => 'Deskripsi kategori harus berupa string',
            'description.max' => 'Deskripsi kategori maksimal 255 karakter',
            'image.image' => 'File yang diunggah harus berupa gambar',
            'image.mimes' => 'Format gambar tidak valid. Format yang didukung: jpeg, png, jpg, gif, svg, webp',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Check if category name already exists
        $existingCategory = Category::where('name', $request->input('name'))
            ->where('store_id', $store->id)
            ->first();

        if ($existingCategory) {
            return ResponseFormatter::error('Nama kategori sudah ada', 409);
        }

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('store/' . $store->id . '/category');
            } else {
                $imagePath = null;
            }

            DB::beginTransaction();

            // Create category
            $category = Category::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'image' => $imagePath,
                'store_id' => $store->id,
            ]);

            DB::commit();

            return ResponseFormatter::success(
                $category,
                'Kategori berhasil dibuat',
                201,
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ResponseFormatter::error('Gagal membuat kategori', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store()->first();

        // Check if user is community
        if ($user->role_id == 3) {
            $store = Store::where('is_community', 1)->first();
            $store = $user->store->first();
            $store = $user->store->first();
        }

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check store activation
        if (!$store->activated_at) {
            return ResponseFormatter::error('Toko belum diaktifkan.', 403);
        }

        // Authorization check
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        // Find category
        $category = Category::with('products')->find($id);

        if (!$category) {
            return ResponseFormatter::error('Kategori tidak ditemukan', 404);
        }

        // Check if category belongs to the store
        if ($category->store_id !== $store->id) {
            return ResponseFormatter::error('Kategori tidak ditemukan', 404);
        }

        return ResponseFormatter::success(
            $category,
            'Kategori berhasil ditemukan',
            200,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);

        // Check if user is community
        if ($user->role_id == 3) {
            $store = Store::where('is_community', 1)->first();
        } else {
            $store = $user->store[0];
        }

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check store activation
        if (!$store->activated_at) {
            return ResponseFormatter::error('Toko belum diaktifkan.', 403);
        }

        // Authorization check
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'name.required' => 'Nama kategori tidak boleh kosong',
            'name.string' => 'Nama kategori harus berupa string',
            'name.max' => 'Nama kategori maksimal 255 karakter',
            'description.string' => 'Deskripsi kategori harus berupa string',
            'description.max' => 'Deskripsi kategori maksimal 255 karakter',
            'image.image' => 'File yang diunggah harus berupa gambar',
            'image.mimes' => 'Format gambar tidak valid. Format yang didukung: jpeg, png, jpg, gif, svg, webp',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Check if category name already exists
        $existingCategory = Category::where('name', $request->input('name'))
            ->where('store_id', $store->id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingCategory) {
            return ResponseFormatter::error('Nama kategori sudah ada', 409);
        }

        try {
            // Find category
            $category = Category::find($id);

            if (!$category) {
                return ResponseFormatter::error('Kategori tidak ditemukan', 404);
            }

            // Check if category belongs to the store
            if ($category->store_id !== $store->id) {
                return ResponseFormatter::error('Kategori tidak ditemukan', 404);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image) {
                    Storage::delete($category->image);
                }

                $imagePath = $request->file('image')
                    ->store('store/' . $store->id . '/category');
            } else {
                $imagePath = $category->image;
            }

            DB::beginTransaction();

            // Update category
            $category->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'image' => $imagePath,
            ]);

            DB::commit();

            return ResponseFormatter::success(
                $category,
                'Kategori berhasil diperbarui',
                200,
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ResponseFormatter::error('Gagal memperbarui kategori', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);

        // Check if user is community
        if ($user->role_id == 3) {
            $store = Store::where('is_community', 1)->first();
        } else {
            $store = $user->store[0];
        }

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check store activation
        if (!$store->activated_at) {
            return ResponseFormatter::error('Toko belum diaktifkan.', 403);
        }

        // Authorization check
        $allowedRoles = [1, 2, 3];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        // Find category
        $category = Category::find($id);

        if (!$category) {
            return ResponseFormatter::error('Kategori tidak ditemukan', 404);
        }

        // Check if category belongs to the store
        if ($category->store_id !== $store->id) {
            return ResponseFormatter::error('Kategori tidak ditemukan', 404);
        }

        // Delete image if exists
        if ($category->image && Storage::exists($category->image)) {
            Storage::delete($category->image);
        }

        // Delete category
        $category->delete();

        return ResponseFormatter::success(
            null,
            'Kategori berhasil dihapus',
            200,
        );
    }

    /**
     * Get category dropdown.
     */
    public function dropdown(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by');
        $sortOrder = $request->input('sort_order', 'asc');
        $storeId = $request->input('store_id');

        $user = Auth::user();
        $user = User::find($user->id);
        $store = $user->store()->first();

        $categories = Category::query();

        if ($user->role_id > 3) {
            if (!$store) {
                return ResponseFormatter::error('Anda belum memiliki toko', 404);
            }

            $categories->where('store_id', $store->id);
        } elseif ($user->role_id == 3) {
            $store = Store::where('is_community', 1)->first();

            if (!$store) {
                return ResponseFormatter::error('Anda belum memiliki toko', 404);
            }

            $categories->where('store_id', $store->id);
        }

        if ($storeId) $categories->where('store_id', $storeId);

        if ($search) {
            $categories->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($sortBy) $categories->orderBy($sortBy, $sortOrder);
        if ($limit) $categories->limit($limit);


        $categories = $categories->get();

        return ResponseFormatter::success(
            $categories,
            'Daftar kategori berhasil ditemukan',
            200,
        );
    }
}
