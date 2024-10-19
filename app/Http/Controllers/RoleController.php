<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::all();

        return ResponseFormatter::success($roles, 'Data role berhasil diambil');
    }
}
