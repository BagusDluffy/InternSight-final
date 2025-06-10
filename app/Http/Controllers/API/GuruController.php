<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuruController extends Controller
{
    // API untuk mendapatkan semua data guru
    public function index()
    {
        $guru = Guru::all(); // Ambil semua data guru
        return response()->json($guru, 200);
    }

    // API untuk mendapatkan data guru berdasarkan ID
    public function show($id)
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return response()->json(['message' => 'Guru not found'], 404);
        }

        return response()->json($guru, 200);
    }

    // API untuk membuat data guru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_guru' => 'required|string|max:255',
            'email' => 'required|email|unique:guru,email',
            'password' => 'required|string|min:6',
            'nip' => 'nullable|string|max:50', // Tambahkan validasi untuk NIP
            'no_hp' => 'nullable|string|max:15', // Tambahkan validasi untuk No HP
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        $guru = Guru::create([
            'nama_guru' => $validatedData['nama_guru'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'nip' => $validatedData['nip'] ?? null, // Tambahkan NIP
            'no_hp' => $validatedData['no_hp'] ?? null, // Tambahkan No HP
        ]);

        return response()->json($guru, 201);
    }

    // API untuk memperbarui data guru
    public function update(Request $request, $id)
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return response()->json(['message' => 'Guru not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_guru' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:guru,email,'.$id,
            'password' => 'sometimes|required|string|min:6',
            'nip' => 'nullable|string|max:50', // Validasi untuk NIP
            'no_hp' => 'nullable|string|max:15', // Validasi untuk No HP
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        // Update data guru
        if (isset($validatedData['nama_guru'])) {
            $guru->nama_guru = $validatedData['nama_guru'];
        }

        if (isset($validatedData['email'])) {
            $guru->email = $validatedData['email'];
        }

        if (isset($validatedData['password'])) {
            $guru->password = bcrypt($validatedData['password']);
        }

        if (isset($validatedData['nip'])) {
            $guru->nip = $validatedData['nip'];
        }

        if (isset($validatedData['no_hp'])) {
            $guru->no_hp = $validatedData['no_hp'];
        }

        $guru->save();

        return response()->json($guru, 200);
    }

    // API untuk menghapus data guru
    public function destroy($id)
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return response()->json(['message' => 'Guru not found'], 404);
        }

        $guru->delete();

        return response()->json(['message' => 'Guru deleted successfully'], 200);
    }
}