@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @php
            $jumlahGuru = DB::table('guru')->count();
            $jumlahJurusan = DB::table('jurusan')->count();
            $jumlahMurid = DB::table('murid')->count();
            $jumlahDudika = DB::table('dudika')->count();
            $jumlahMagang = DB::table('magang')->count();
            $jumlahLaporan = DB::table('laporan')->count();

            $jurusanMagang = DB::table('jurusan')
            ->leftJoin('magang', 'jurusan.id', '=', 'magang.jurusan_id')
            ->select('jurusan.jurusan', DB::raw('COUNT(magang.id) as total'))
            ->groupBy('jurusan.jurusan')
            ->get();
            @endphp

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Statistik Internship Management
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Guru</span>
                                            <span class="info-box-number">{{ $jumlahGuru }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Murid</span>
                                            <span class="info-box-number">{{ $jumlahMurid }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-briefcase"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Dudika</span>
                                            <span class="info-box-number">{{ $jumlahDudika }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="info-box bg-danger">
                                        <span class="info-box-icon"><i class="fas fa-file-pdf"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Laporan</span>
                                            <span class="info-box-number">{{ $jumlahLaporan }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Distribusi Magang per Jurusan
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="magangChart" height="230"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Daftar Magang Terbaru
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @php
                                $recentMagangs = App\Models\Magang::with(['jurusan', 'dudika'])
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                                @endphp
                                @foreach($recentMagangs as $magang)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $magang->jurusan->jurusan }}</strong>
                                        <br>
                                        <small>{{ $magang->dudika->dudika }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $magang->murid->count() }} Murid</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var jurusanMagang = <?php echo json_encode($jurusanMagang); ?>;

        var ctx = document.getElementById('magangChart').getContext('2d');
        var magangChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: jurusanMagang.map(function(data) {
                    return data.jurusan;
                }),
                datasets: [{
                    label: 'Jumlah Magang',
                    data: jurusanMagang.map(function(data) {
                        return data.total;
                    }),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection