<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $roles = Role::query();

        if ($search) {
            $roles->where('name', 'like', "%$search%");
        }

        $roles->get();

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Data role berhasil ditemukan',
        );
    }

    public function dropdown(Request $request)
    {
        $limit = $request->input('limit');
        $search = $request->input('search');

        $roles = Role::query();

        if ($search) {
            $roles->where('name', 'like', "%$search%");
        }

        if ($limit) {
            $roles->limit($limit);
        }

        $roles = $roles->get();

        return ResponseFormatter::success($roles, 'Data role berhasil ditemukan');
    }
}
