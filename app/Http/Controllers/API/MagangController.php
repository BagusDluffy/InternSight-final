<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Magang;
use App\Models\Laporan;
use App\Models\Murid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MagangController extends Controller
{
    public function getMagangByGuru($guruId)
    {
        try {
            // Aktifkan query log
            DB::enableQueryLog();

            $magang = Magang::with([
                'dudika', 
                'murid' => function($query) {
                    $query->select('murid.id', 'murid.nama_murid'); 
                }
            ])
            ->where('guru_id', $guruId)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'guru_id' => $item->guru_id,
                    'dudika_id' => $item->dudika_id,
                    'dudika' => $item->dudika,
                    'murid' => $item->murid->map(function($murid) {
                        return [
                            'id' => $murid->id,
                            'nama_murid' => $murid->nama_murid
                        ];
                    })
                ];
            });

            // Log query yang dijalankan
            $queries = DB::getQueryLog();
            Log::info('Magang Queries:', $queries);

            // Log data magang
            Log::info('Magang Data for Guru ID ' . $guruId, [
                'count' => $magang->count(),
                'data' => $magang->toArray()
            ]);
    
            return response()->json($magang);
            
        } catch (\Exception $e) {
            // Logging yang lebih komprehensif
            Log::error('Error in getMagangByGuru', [
                'guru_id' => $guruId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentsByDudika($dudika_id)
    {
        try {
            // Aktifkan query log
            DB::enableQueryLog();

            $students = Murid::whereHas('magang', function ($query) use ($dudika_id) {
                $query->where('dudika_id', $dudika_id);
            })->get();

            // Log query yang dijalankan
            $queries = DB::getQueryLog();
            Log::info('Students Queries:', $queries);

            // Log data siswa
            Log::info('Students for Dudika ID ' . $dudika_id, [
                'count' => $students->count(),
                'data' => $students->toArray()
            ]);

            return response()->json($students);
        } catch (\Exception $e) {
            // Logging yang lebih detail
            Log::error('Error fetching students', [
                'dudika_id' => $dudika_id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Error fetching students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLaporanByGuru($guruId)
    {
        try {
            // Aktifkan query log
            DB::enableQueryLog();

            $laporan = Laporan::where('guru_id', $guruId)->get();

            // Log query yang dijalankan
            $queries = DB::getQueryLog();
            Log::info('Laporan Queries:', $queries);

            // Log data laporan
            Log::info('Laporan Data for Guru ID ' . $guruId, [
                'count' => $laporan->count(),
                'data' => $laporan->toArray()
            ]);

            return response()->json($laporan);
        } catch (\Exception $e) {
            // Logging yang lebih komprehensif
            Log::error('Error in getLaporanByGuru', [
                'guru_id' => $guruId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}