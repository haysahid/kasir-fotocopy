<?php

namespace App\Http\Controllers\API;

use App\Models\Store;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Models\User;
use App\Models\UserStore;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $store_id = $request->input('store_id');
        $category = $request->input('category');
        $search = $request->input('search');
        $code = $request->input('code');
        $limit = $request->input('limit', 10);

        $show_disabled = $request->input('show_disabled', 0);
        $show_detail = $request->input('show_detail', 0);

        if ($show_detail) {
            $products = ProductDetail::query();
        } else {
            $products = Product::query();
        }

        if ($user->role_id > 3) {
            $store = User::with('store')->find($user->id)->store->first();

            if (!$store) {
                return ResponseFormatter::error('Anda belum memiliki toko.', 404);
            }

            $products->where('products.store_id', $store->id);
        }

        if ($store_id) {
            $products->where('products.store_id', $store_id);
        }

        if ($category) {
            $products->where('products.category', $category);
        }

        if ($search) {
            $products->where(function ($query) use ($search) {
                $query->where('products.code', $search)
                    ->orWhere('products.name', 'like', '%' . $search . '%')
                    ->orWhere('products.description', 'like', '%' . $search . '%');
            });
        }

        if ($code) {
            $products->where('products.code', $code);
        }

        if (!$show_disabled) {
            $products->whereNull('products.disabled_at');
        }

        $products->with(['store', 'product_images'])->select('products.*')->latest();

        return ResponseFormatter::success(
            $products->paginate($limit),
            'Daftar produk berhasil ditemukan.',
            200
        );
    }

    /**
     * Product a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        // Check store activation
        if (!$store->activated_at) {
            return ResponseFormatter::error('Toko belum diaktifkan.', 403);
        }

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }


        $request->validate([
            'code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'purchase_price' => 'required|integer',
            'selling_price' => 'required|integer',
            'initial_stock' => 'required|integer',
            'unit' => 'required|string|max:30',
            'category' => 'nullable|string|max:255',
            'expired_at' => 'nullable|string|max:255',

            // product_images
            'product_images' => 'nullable|array',
            'product_images.*' => 'nullable|file',
        ]);

        // Check code uniqueness
        $code = $request->input('code');
        $products = Product::where('code', $code)->where('store_id', $store->id)->get();

        if ($products->count() > 0 && $code) {
            return ResponseFormatter::error('Kode produk sudah digunakan.', 409);
        }

        try {
            $product = Product::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'purchase_price' => $request->input('purchase_price'),
                'selling_price' => $request->input('selling_price'),
                'initial_stock' => $request->input('initial_stock'),
                'unit' => strtolower($request->input('unit')),
                'category' => $request->input('category'),
                'expired_at' => $request->input('expired_at') ? date('Y-m-d H:i:s', strtotime($request->input('expired_at'))) : null,
                'store_id' => $store->id,
            ]);

            if ($request->input('expired_at')) {
                $product->expired_at = date('Y-m-d H:i:s', strtotime($request->input('expired_at')));
                $product->save();
            }

            // Product images
            if ($request->hasFile('product_images')) {
                $files = $request->file('product_images');

                ProductImageController::addProductImages($product->id, $files);
            }

            $product = Product::with(['store', 'product_images'])->find($product->id);

            return ResponseFormatter::success([
                'product' => $product,
            ], 'Produk berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Produk gagal ditambahkan.' . $error, 500);
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

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        $product = Product::with(['store', 'product_images'])->where('store_id', $store->id)->find($id);
        // Check product availability
        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $store = Store::find($product->store_id);

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        return ResponseFormatter::success([
            'product' => $product,
        ], 'Produk berhasil ditemukan.', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        $product = Product::where('store_id', $store->id)->find($id);
        // Check product availability
        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $store = Store::find($product->store_id);

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        // Check code uniqueness
        $code = $request->input('code');
        $products = Product::where('code', $code)->where('store_id', $store->id)->where('id', '!=', $id)->get();
        $existingProduct = Product::where('id', $id)->first();

        if ($products->count() > 0 && $existingProduct->code != $code && $code) {
            return ResponseFormatter::error('Kode produk sudah digunakan.', 409);
        }

        $request->validate([
            'code' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|integer',
            'selling_price' => 'nullable|integer',
            'initial_stock' => 'nullable|integer',
            'unit' => 'nullable|string|max:30',
            'category' => 'nullable|string|max:255',
            'expired_at' => 'nullable|string|max:255',
        ]);

        try {
            // Update product
            $product->update($request->all());

            $product = Product::with(['store', 'product_images'])->find($product->id);

            return ResponseFormatter::success([
                'product' => $product,
            ], 'Produk berhasil diubah.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Produk gagal diubah.' . $error, 500);
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

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        $product = Product::with(['store', 'product_images'])->where('store_id', $store->id)->find($id);
        // Check product availability
        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $store = Store::find($product->store_id);

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $product->delete();

        return ResponseFormatter::success(
            $product->id,
            'Produk berhasil dihapus.',
            200
        );
    }

    /**
     * Disable product.
     */
    public function disable(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        $product = Product::with(['store', 'product_images'])->where('store_id', $store->id)->find($id);
        // Check product availability
        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $store = Store::find($product->store_id);

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $product->update([
            'disabled_at' => now(),
        ]);

        return ResponseFormatter::success(
            $product,
            'Produk berhasil dinonaktifkan.',
            200
        );
    }

    /**
     * Enable product.
     */
    public function enable(string $id)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0];

        // Store availability check
        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        $product = Product::with(['store', 'product_images'])->where('store_id', $store->id)->find($id);
        // Check product availability
        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $store = Store::find($product->store_id);

        // Authorization check
        $allowedRoles = [1, 2];
        $isOwner = UserStore::where('store_id', $store->id)->where('user_id', $user->id)->first();

        if (!in_array($user->role_id, $allowedRoles) && (!$isOwner && (!in_array($user->role_id, [4, 5])))) {
            return ResponseFormatter::error("Anda tidak memiliki hak akses. $isOwner", 401);
        }

        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $product->update([
            'disabled_at' => null,
        ]);

        return ResponseFormatter::success(
            $product,
            'Produk berhasil diaktifkan.',
            200
        );
    }
}
