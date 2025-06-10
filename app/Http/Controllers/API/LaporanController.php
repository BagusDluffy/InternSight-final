<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Magang;

class LaporanController extends Controller
{
    // LaporanController.php

    public function index()
    {
        try {
            $laporan = Laporan::with('magang')
                ->select('id', 'magang_id', 'tanggal_kunjungan', 'keterangan', 'laporan_siswa', 'created_at', 'updated_at')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $laporan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi request
            $validated = $request->validate([
                'magang_id' => 'required|exists:magang,id',
                'tanggal_kunjungan' => 'required|date',
                'keterangan' => 'required|string',
                'laporan_siswa' => 'required|json',
                'foto' => 'required|image|max:5120',
                'tanda_tangan' => 'required|image|max:5120',
            ]);
    
            // Buat direktori langsung di public/storage
            $fotoPath = null;
            $tandaTanganPath = null;
    
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoName = time() . '_' . $foto->getClientOriginalName();
                $foto->move(public_path('storage/laporan_foto'), $fotoName);
                $fotoPath = 'laporan_foto/' . $fotoName;
            }
    
            if ($request->hasFile('tanda_tangan')) {
                $tandaTangan = $request->file('tanda_tangan');
                $tandaTanganName = time() . '_' . $tandaTangan->getClientOriginalName();
                $tandaTangan->move(public_path('storage/tanda_tangan'), $tandaTanganName);
                $tandaTanganPath = 'tanda_tangan/' . $tandaTanganName;
            }
    
            // Buat record laporan
            $laporan = Laporan::create([
                'magang_id' => $validated['magang_id'],
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'keterangan' => $validated['keterangan'],
                'laporan_siswa' => $validated['laporan_siswa'],
                'foto' => $fotoPath,
                'tanda_tangan' => $tandaTanganPath
            ]);
    
            return response()->json([
                'message' => 'Laporan berhasil disimpan',
                'data' => $laporan
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Error saving laporan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menyimpan laporan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDudika(Request $request)
    {
        $user = $request->user(); // Mendapatkan user yang login
        $guruId = $user->id; // Asumsikan user adalah guru

        // Cari magang yang terkait dengan guru ini
        $magang = Magang::with('dudika')
            ->where('guru_id', $guruId)
            ->get();

        // Ambil data dudika dari magang
        $dudikaList = $magang->pluck('dudika');

        return response()->json([
            'data' => $dudikaList,
        ]);
    }

    public function getMagangId(Request $request)
    {
        $validated = $request->validate([
            'dudika_id' => 'required|exists:dudika,id',
        ]);

        $magang = Magang::where('dudika_id', $validated['dudika_id'])->first();

        if (!$magang) {
            return response()->json(['message' => 'Magang tidak ditemukan'], 404);
        }

        return response()->json(['data' => $magang], 200);
    }

    // Tambahkan method ini di LaporanController.php
public function getLaporanByMagangId($magangId)
{
    try {
        $laporan = Laporan::where('magang_id', $magangId)
            ->select('id', 'magang_id', 'tanggal_kunjungan', 'keterangan', 'laporan_siswa', 'tanda_tangan', 'created_at', 'updated_at')
            ->orderBy('tanggal_kunjungan', 'desc')
            ->get();

        if ($laporan->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No laporan found for this magang',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => $laporan
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
