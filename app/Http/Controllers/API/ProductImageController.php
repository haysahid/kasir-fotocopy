<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(String $productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $productImages = ProductImage::where('product_id', $productId)->get();

        return ResponseFormatter::success([
            'total' => count($productImages),
            'product_images' => $productImages,
        ], 'Gambar produk berhasil ditemukan.', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, String $productId)
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

        $product = Product::find($productId);

        if (!$product) {
            return ResponseFormatter::error('Produk tidak ditemukan.', 404);
        }

        $request->validate([
            'product_images' => 'nullable|array',
            'product_images.*' => 'nullable|file|mimes:jpg,jpeg,png,webp',
        ], [
            'product_images.*.uploaded' => 'Gambar produk harus berupa file gambar.',
        ]);

        try {
            if ($request->hasFile('product_images')) {
                $files = $request->file('product_images');

                $productImages = self::addProductImages($productId, $files, $store);
            }

            return ResponseFormatter::success([
                'total' => count($productImages),
                'product_images' => $productImages,
            ], 'Gambar produk berhasil ditambahkan.', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Gambar produk gagal ditambahkan.' . $error, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $productId, string $id)
    {
        $productImage = ProductImage::where('product_id', $productId)->where('id', $id)->first();

        if (!$productImage) {
            return ResponseFormatter::error('Produk atau gambar produk tidak ditemukan.', 404);
        }

        $request->validate([
            'product_image' => 'required|file',
        ]);

        try {
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $productImage = self::updateProductImage($id, $file);
            }

            return ResponseFormatter::success([
                'product_image' => $productImage,
            ], 'Gambar produk berhasil diubah.', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error('Terjadi kesalahan. Gambar produk gagal diubah.' . $error, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $productId, string $id)
    {
        $productImage = ProductImage::where('product_id', $productId)->where('id', $id)->first();

        if (!$productImage) {
            return ResponseFormatter::error('Produk atau gambar produk tidak ditemukan.', 404);
        }

        // Delete old image
        if ($productImage->image) {
            Storage::delete($productImage->image);
        }

        $productImage->delete();

        return ResponseFormatter::success(
            $productImage->id,
            'Gambar produk berhasil dihapus.',
            200
        );
    }

    public static function addProductImages($productId, $files, $store)
    {
        foreach ($files as $file) {
            if (!$file) continue;

            $image_path = $file->store('store/' . $store->id . '/product');

            ProductImage::create([
                'image' => $image_path,
                'product_id' => $productId,
                'store_id' => $store->id,
            ]);
        }

        $productImages = ProductImage::where('product_id', $productId)->get();

        return $productImages;
    }

    public static function updateProductImage($productImageId, $file)
    {
        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        $productImage = ProductImage::find($productImageId);

        // Delete old image
        if ($productImage->image) {
            Storage::delete($productImage->image);
        }

        // Store image 
        $image_path = $file->store('store/' . $store->id . '/product');

        // Add to database
        $productImage->update([
            'image' => $image_path,
        ]);

        $productImage = ProductImage::find($productImageId);

        return $productImage;
    }
}
