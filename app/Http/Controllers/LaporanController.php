<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'magang_id' => 'required|exists:magang,id',
            'tanggal_kunjungan' => 'required|date',
            'keterangan' => 'required|string',
            'laporan_siswa' => 'required|array',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanda_tangan' => 'required|string',
        ]);

        // Simpan foto
        $fotoPath = $request->file('foto')->store('laporan_foto', 'public');

        // Simpan tanda tangan
        $tandaTanganPath = null;
        if ($request->tanda_tangan) {
            $tandaTanganPath = 'tanda_tangan/' . uniqid() . '.png';
            $tandaTanganData = explode(',', $request->tanda_tangan)[1];
            Storage::disk('public')->put($tandaTanganPath, base64_decode($tandaTanganData));
        }

        // Simpan laporan
        $laporan = Laporan::create([
            'magang_id' => $request->magang_id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'keterangan' => $request->keterangan,
            'laporan_siswa' => json_encode($request->laporan_siswa), // Simpan sebagai JSON
            'foto' => $fotoPath,
            'tanda_tangan' => $tandaTanganPath,
        ]);

        return response()->json(['message' => 'Laporan berhasil disimpan', 'laporan' => $laporan], 201);
    }
}