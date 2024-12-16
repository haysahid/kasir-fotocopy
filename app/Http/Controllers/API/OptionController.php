<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Option;
use Exception;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $options = Option::query();

        if ($search) {
            $options->where('name', 'like', "%$search%");
        }

        $options->latest();

        return ResponseFormatter::success(
            $options->paginate($limit),
            'Data opsi berhasil ditemukan',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        $option = Option::where('name', $validatedData['name'])->first();

        if ($option) {
            return ResponseFormatter::error('Opsi sudah ada', 409);
        }

        try {
            $option = Option::create($validatedData);

            $option = Option::find($option->id);

            return ResponseFormatter::success($option, 'Opsi berhasil ditambahkan');
        } catch (Exception $error) {
            return ResponseFormatter::error('Opsi gagal ditambahkan', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $option = Option::find($id);

        if (!$option) {
            return ResponseFormatter::error('Opsi tidak ditemukan', 404);
        }

        return ResponseFormatter::success($option, 'Opsi berhasil ditemukan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $option = Option::find($id);

        if (!$option) {
            return ResponseFormatter::error('Opsi tidak ditemukan', 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $option->update($validatedData);

            return ResponseFormatter::success($option, 'Opsi berhasil diperbarui');
        } catch (Exception $error) {
            return ResponseFormatter::error('Opsi gagal diperbarui', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $option = Option::find($id);

        if (!$option) {
            return ResponseFormatter::error('Opsi tidak ditemukan', 404);
        }

        try {
            $option->delete();

            return ResponseFormatter::success(null, 'Opsi berhasil dihapus');
        } catch (Exception $error) {
            return ResponseFormatter::error('Opsi gagal dihapus', 500);
        }
    }

    /**
     * Get option dropdown.
     */
    public function dropdown(Request $request)
    {
        $limit = $request->input('limit');
        $search = $request->input('search');

        $options = Option::query();

        if ($search) {
            $options->where('name', 'like', "%$search%");
        }

        if ($limit) {
            $options->limit($limit);
        }

        $options = $options->latest()->get();

        return ResponseFormatter::success($options, 'Data opsi berhasil ditemukan');
    }
}
