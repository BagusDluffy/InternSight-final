<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari guru berdasarkan email
        $guru = Guru::where('email',    $request->email)->first();

        if (!$guru || !Hash::check($request->password, $guru->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Buat token autentikasi menggunakan Sanctum
        $token = $guru->createToken('authToken')->plainTextToken;
        $name = $guru->nama_guru;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'user' => $guru,
            'token' => $token,
            'name' => $name,
        ], 200);
    }

    public function validateToken(Request $request)
    {
        try {
            // Dapatkan user yang sedang terotentikasi
            $user = $request->user();

            // Pastikan user adalah guru
            $guru = Guru::find($user->id);

            if (!$guru) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Guru tidak ditemukan'
                ], 401);
            }

            // Kembalikan informasi guru
            return response()->json([
                'valid' => true,
                'message' => 'Token is valid',
                'user' => [
                    'id' => $guru->id,
                    'nama_guru' => $guru->nama_guru,
                    'email' => $guru->email,
                    // Tambahkan field lain yang diperlukan
                ]
            ], 200);
        } catch (\Exception $e) {
            // Tangani error
            return response()->json([
                'valid' => false,
                'message' => 'Kesalahan validasi token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
