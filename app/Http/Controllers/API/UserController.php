<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Ambil semua user tanpa password
        $users = User::select('id', 'name', 'email', 'created_at')->get();

        return response()->json($users);
    }
}