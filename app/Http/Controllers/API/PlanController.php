<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Exception;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $plans = Plan::query();

        if ($search) {
            $plans->where('name', 'like', "%$search%");
        }

        $plans->with('options')->latest();

        return ResponseFormatter::success(
            $plans->paginate($limit),
            'Data paket berhasil ditemukan'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'is_active' => 'required|boolean',
            'options' => 'required|array',
            'options.*' => 'required|integer|exists:options,id',
        ]);

        try {
            $plan = Plan::create($validatedData);

            $plan->options()->attach($validatedData['options'], [
                'date_added' => now(),
            ]);

            $plan = Plan::with('options')->find($plan->id);

            return ResponseFormatter::success($plan, 'Data paket berhasil ditambahkan');
        } catch (Exception $error) {
            return ResponseFormatter::error('Data paket gagal ditambahkan', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = Plan::with('options')->find($id);

        if (!$plan) {
            return ResponseFormatter::error('Data paket tidak ditemukan', 404);
        }

        return ResponseFormatter::success($plan, 'Data paket berhasil ditemukan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return ResponseFormatter::error('Data paket tidak ditemukan', 404);
        }

        $validatedData = $request->validate([
            'name' => 'string',
            'price' => 'integer',
            'is_active' => 'boolean',
            'options' => 'array',
            'options.*' => 'integer|exists:options,id',
        ]);

        try {
            $plan->update($validatedData);

            if (isset($validatedData['options'])) {
                $plan->options()->syncWithPivotValues($validatedData['options'], [
                    'date_added' => now(),
                ]);
            }

            $plan->load('options');

            return ResponseFormatter::success($plan, 'Data paket berhasil diperbarui');
        } catch (Exception $error) {
            return ResponseFormatter::error("Data paket gagal diperbarui: $error", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return ResponseFormatter::error('Data paket tidak ditemukan', 404);
        }

        try {
            $plan->delete();

            return ResponseFormatter::success(null, 'Data paket berhasil dihapus');
        } catch (Exception $error) {
            return ResponseFormatter::error('Data paket gagal dihapus', 500);
        }
    }

    /**
     * Get plan dropdown.
     */
    public function dropdown(Request $request)
    {
        $limit = $request->input('limit');
        $search = $request->input('search');

        $plans = Plan::query();

        $plans->with('options');

        if ($search) {
            $plans->where('name', 'like', "%$search%");
        }

        if ($limit) {
            $plans->limit($limit);
        }

        $plans = $plans->latest()->get();

        return ResponseFormatter::success($plans, 'Data paket berhasil ditemukan');
    }
}
