@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Magang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Data Magang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container">
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 d-flex">
                        <a href="{{ route('magang.create') }}" class="btn btn-primary btn-sm mr-2">
                            <i class="fas fa-plus"></i> Tambah Magang
                        </a>
                        <a href="{{ route('magang.download-template') }}" class="btn btn-info btn-sm mr-2">
                            <i class="fas fa-file-excel"></i> Download Template
                        </a>
                        <a href="{{ route('magang.export') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        <form action="{{ route('magang.import') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                            @csrf
                            <div class="input-group input-group-sm">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                                    <label class="custom-file-label text-left" for="importFile">Pilih file Excel</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-upload"></i> Import
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="btn-group ml-2" role="group">
                            <button id="deleteSelected" class="btn btn-danger btn-sm" disabled>
                                <i class="fas fa-trash"></i> Hapus Terpilih
                            </button>
                            <button id="deleteAll" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="multiDeleteForm" action="{{ route('magang.multi-delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <table class="table table-bordered table-striped" id="magangTable">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th class="text-center align-middle">No</th>
                                <th class="text-center align-middle">Jurusan</th>
                                <th class="text-center align-middle">Kelas</th>
                                <th class="text-center align-middle">Nama Murid</th>
                                <th class="text-center align-middle">DUDIKA</th>
                                <th class="text-center align-middle">Guru Pembimbing</th>
                                <th class="text-center align-middle">Periode</th>
                                <th class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($magang as $key => $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <input type="checkbox" name="selected_ids[]" value="{{ $item->id }}" class="checkbox-item">
                                </td>
                                <td class="text-center align-middle">{{ $key + 1 }}</td>
                                <td class="text-center align-middle">{{ $item->jurusan->jurusan }}</td>
                                <td class="text-center align-middle">{{ $item->kelas }}</td>
                                <td class="text-start">
                                    @if ($item->murid->count() > 0)
                                    <ul class="list-unstyled">
                                        @foreach ($item->murid as $murid)
                                        <li>• {{ $murid->nama_murid }}</li>
                                        @endforeach
                                    </ul>
                                    @else
                                    {{ $item->murid->isEmpty() ? 'Tidak ada murid' : '' }}
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->dudika->dudika }}</td>
                                <td class="text-center align-middle">{{ $item->guru->nama_guru }}</td>
                                <td class="text-center align-middle">{{ $item->periode }}</td>
                                <td class="text-center align-middle">
                                    <div class="btn-group" role="group">
                                        <a href="#" class="btn btn-info btn-sm mr-1 btn-print"
                                            data-print-url="{{ route('magang.print-data', $item) }}">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('magang.edit', $item) }}"
                                            class="btn btn-warning btn-sm mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('magang.destroy', $item) }}" method="POST"
                                            class="delete-form d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm delete-button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </section>
</div>

<style>
    /* Styling untuk tombol agar ukurannya sama dengan input file */
    .btn-primary,
    .btn-info,
    .btn-success,
    .btn-danger {
        font-size: 16px !important;
        padding: 10px 15px !important;
        height: 38px !important;
        display: flex;
        align-items: center;
    }

    /* Sesuaikan tombol dalam btn-group */
    .btn-group .btn {
        font-size: 16px !important;
        padding: 10px 15px !important;
        height: 38px !important;
    }

    .custom-file {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .custom-file-input {
        height: 38px !important;
    }

    .custom-file-label {
        font-size: 16px !important;
        height: 38px !important;
        padding: 6px 12px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-file-label::after {
        height: 38px !important;
        padding: 6px 12px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Tampilan layar besar (1080p ke atas) */
    @media (min-width: 1920px) {

        /* Responsive button sizing */
        .btn {
            font-size: 16px !important;
            padding: 10px 15px !important;
        }

        /* Perbesar teks untuk judul */
        h1 {
            font-size: 32px !important;
            font-weight: bold !important;
        }

        /* Perbesar teks pada tabel */
        table th,
        table td {
            font-size: 18px !important;
            padding: 12px !important;
        }

        /* Perbesar teks pada label */
        .form-group label {
            font-size: 18px !important;
            font-weight: 600 !important;
        }

        /* Perbesar teks pada input */
        .form-control {
            font-size: 18px !important;
            padding: 10px !important;
            border-radius: 8px !important;
        }

        /* Custom file input styling */
        .custom-file-input,
        .custom-file-label {
            font-size: 18px !important;
            padding: 10px !important;
        }

        .custom-file-label::after {
            padding: 10px 15px !important;
        }

        /* Input group styling */
        .input-group {
            font-size: 16px !important;
        }

        /* Sesuaikan tombol aksi pada tabel */
        .btn-group .btn {
            font-size: 16px !important;
            padding: 8px 12px !important;
        }
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .card-body .row {
            flex-direction: column;
            align-items: stretch;
        }

        .card-body .row>div {
            margin-bottom: 10px;
            text-align: center !important;
        }

        .card-body .row>div .btn-group {
            display: flex;
            justify-content: center;
        }

        .input-group {
            width: 100% !important;
        }
    }
</style>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.28/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.28/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#magangTable').DataTable({
            responsive: true,
            autoWidth: false,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            order: [],
            language: {
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(disaring dari _MAX_ total data)",
                search: "Cari:",
                paginate: {
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            columnDefs: [{
                orderable: false,
                targets: [0, 8] // Disable sorting for checkbox and action columns
            }]
        });

        $('#selectAll').on('change', function() {
            $('.checkbox-item').prop('checked', this.checked).change();
        });

        $(document).on('change', '.checkbox-item', function() {
            $('#selectAll').prop('checked', $('.checkbox-item:checked').length === $('.checkbox-item').length);
            $('#deleteSelected').prop('disabled', $('.checkbox-item:checked').length === 0);
        });

        // Delete Selected Confirmation
        $('#deleteSelected').on('click', function() {
            const selectedCount = $('.checkbox-item:checked').length;
            Swal.fire({
                title: 'Hapus DUDIKA Terpilih',
                text: `Anda akan menghapus ${selectedCount} DUDIKA. Apakah Anda yakin?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#multiDeleteForm').submit();
                }
            });
        });

        // Individual Delete Confirmation
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Data DUDIKA akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Delete All Confirmation
        $('#deleteAll').on('click', function() {
            Swal.fire({
                title: 'Hapus Semua DUDIKA',
                text: 'Anda akan menghapus SEMUA data DUDIKA. Aksi ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('dudika.delete-all') }}",
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', 'Semua data DUDIKA telah dihapus.', 'success')
                                .then(() => location.reload());
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                }
            });
        });

        // Replace the existing .btn-print click handler with this improved version
        $('.btn-print').on('click', function(e) {
            e.preventDefault();
            const url = $(this).data('print-url');
            const dudikaId = $(this).data('dudika-id');

            // Show loading indicator
            Swal.fire({
                title: 'Menyiapkan laporan...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    dudika_id: dudikaId
                },
                dataType: 'json',
                success: function(response) {
                    // Hide loading indicator
                    Swal.close();

                    // Check for errors
                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error
                        });
                        return;
                    }

                    // Create report HTML
                    let laporanHtml = response.laporan.length > 0 ?
                        response.laporan.map((lap, index) => {
                            let keteranganDetail = lap.keterangan || 'Belum ada keterangan';
                            return `
            <tr>
                <td class="text-center align-middle">${index + 1}</td>
                <td class="text-center align-middle">${lap.hari_tanggal || 'Belum diisi'}</td>
                <td class="text-center align-middle">${keteranganDetail}</td>
                <td class="text-center align-middle">
                    ${lap.foto ? `<img src="${lap.foto}" alt="Foto Kunjungan" class="img-fluid print-image">` : 'Tidak ada foto'}
                </td>
                <td class="text-center">
                    ${lap.tanda_tangan ? `<img src="${lap.tanda_tangan}" alt="Tanda Tangan" class="img-fluid print-image">` : 'Tidak ada tanda tangan'}
                </td>
            </tr>`;
                        }).join('') :
                        // If no data, display empty rows
                        `
        <tr>
            <td class="text-center">1</td>
            <td>Belum diisi</td>
            <td>Belum ada keterangan</td>
            <td class="text-center align-middle">Tidak ada foto</td>
            <td>Tidak ada tanda tangan</td>
        </tr>
        <tr>
            <td class="text-center">2</td>
            <td>Belum diisi</td>
            <td>Belum ada keterangan</td>
            <td class="text-center align-middle">Tidak ada foto</td>
            <td>Tidak ada tanda tangan</td>
        </tr>
        <tr>
            <td class="text-center">3</td>
            <td>Belum diisi</td>
            <td>Belum ada keterangan</td>
            <td class="text-center align-middle">Tidak ada foto</td>
            <td>Tidak ada tanda tangan</td>
        </tr>
        <tr>
            <td class="text-center">4</td>
            <td>Belum diisi</td>
            <td>Belum ada keterangan</td>
            <td class="text-center align-middle">Tidak ada foto</td>
            <td>Tidak ada tanda tangan</td>
        </tr>`;

                    // Generate findings HTML
                    let findingsHtml = '';
                    if (response.laporan && response.laporan.length > 0) {
                        // Combine findings from each report
                        const groupedFindings = response.laporan.reduce((acc, lap) => {
                            if (lap.laporan_siswa && lap.laporan_siswa.length > 0) {
                                acc.push(lap.laporan_siswa.filter(item => item).join(', '));
                            }
                            return acc;
                        }, []);

                        findingsHtml = `
        <div class="findings">
            <h5 class="text-bold">Temuan Waktu Kunjungan</h5>
            <ol>
                ${groupedFindings.length > 0 
                    ? groupedFindings.map((finding, index) => `<li>${finding}</li>`).join('') 
                    : `
                    <li>....................................................</li>
                    <li>....................................................</li>
                    <li>....................................................</li>
                    <li>....................................................</li>
                `}
            </ol>
        </div>
    `;
                    } else {
                        findingsHtml = `
        <div class="findings">
            <h5 class="text-bold">Temuan Waktu Kunjungan</h5>
            <ol>
                <li>....................................................</li>
                <li>....................................................</li>
                <li>....................................................</li>
                <li>....................................................</li>
            </ol>
        </div>
    `;
                    }

                    // Full HTML content for the report with improved header styling
                    const htmlContent = `
    <!DOCTYPE html>
<html>
    <head>
        <title>Cetak Laporan Magang</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            @page {
                size: A4;
                margin: 10mm;
            }
            body {
                font-family: Arial, sans-serif;
                padding: 0;
            }
            .borderless-table td {
                border: none;
                padding: 5px 0;
            }
            .table-bordered th, 
            .table-bordered td {
                border: 1px solid black !important;
            }
            .text-bold {
                font-weight: bold;
            }
            .text-center {
                text-align: center;
            }
            .findings {
                margin: -19px 0;
                border: 1px solid black;
                border-top: none;
                padding-top: 5px;
                padding-left: 15px;
            }
            .findings ol {
                margin: 0;
                padding: 0 0 10px 10px;
                list-style-position: inside; 
            }
            .findings li {
                padding: 10px 0;
            }
            .nip-container {
                word-break: break-all;
            }
            .print-image {
                max-width: 100px; 
                max-height: 100px; 
                object-fit: cover;
            }
            table {
                width: 100%;
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
            
            /* Styling khusus untuk informasi perusahaan */
            .company-info td:first-child {
                width: 160px;
                padding-right: 0;
                vertical-align: top; /* Align text to top */
            }
            .company-info td:last-child {
                padding-left: 0;
                vertical-align: top; /* Align text to top */
            }
            
            /* IMPROVED HEADER STYLING WITH CLOSER SPACING */
            .header-title {
                font-size: 1.65rem;
                font-weight: bold;
            }
            
            .header-subtitle {
                font-size: 1.6rem;
                font-weight: bold;
            }
            
            .school-name {
                font-size: 1.55rem;
            }
            
            .school-address {
                font-size: 1.2rem;
            }
            
            /* Remove blue color and underline from email */
            .email-link {
                color: blue;
                text-decoration: underline;
            }
            
            /* Adjusted logo container */
            .logo-container {
                text-align: center;
                padding-right: 0;
            }
            
            .logo-img {
                width: 100px;
                height: auto;
            }
            
            /* Adjust the header layout - make logo closer to text */
            .header .row {
                display: flex;
                align-items: center;
            }
            
            .header .col-2 {
                flex: 0 0 10%;
                max-width: 10%;
                padding-right: 0;
            }
            
            .header .col-10 {
                flex: 0 0 90%;
                max-width: 90%;
                padding-left: 0;
            }
            
            /* Remove default bootstrap gutters to bring logo and text closer */
            .header .row {
                margin-left: 0;
                margin-right: 0;
            }
            
            .header .col-2, .header .col-10 {
                padding: 0;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="header text-center">
                <div class="row">
                    <div class="col-2 logo-container">
                        <img src="{{ asset('assets/laporan-icon.png') }}" alt="Logo SMKN 10" class="logo-img">
                    </div>
                    <div class="col-10">
                        <div class="header-title">PEMERINTAH PROVINSI JAWA TIMUR</div>
                        <div class="header-subtitle">DINAS PENDIDIKAN</div>   
                        <div style="display: flex; justify-content: center; width: 100%;">
                            <div class="school-name">SEKOLAH MENENGAH KEJURUAN NEGERI 10 SURABAYA</div>
                        </div>
                        <div style="display: flex; justify-content: center; width: 100%;">
                            <div class="school-address">JL. KEPUTIH TEGAL TELP.FAX 5939581 EMAIL <span class="email-link">info@smkn10surabaya</span><span?>.sch.id</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <h2 class="text-center text-bold">DAFTAR MONITORING</h2>
                <h2 class="text-center text-bold">PRAKTIK KERJA LAPANGAN</h2>

                <table class="borderless-table company-info">
                    <tr>
                        <td>Nama Perusahaan</td>
                        <td>: ${response.dudika}</td>
                    </tr>
                    <tr>
                        <td>Alamat Perusahaan</td>
                        <td>: ${response.alamat}</td>
                    </tr>
                    <tr>
                        <td>Contact Person</td>
                        <td>: ${response.kontak}</td>
                    </tr>
                </table>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th class="text-center align-middle">No</th>
                            <th class="text-center align-middle">HARI/TANGGAL</th>
                            <th class="text-center align-middle">TUJUAN/KETERANGAN</th>
                            <th class="text-center align-middle">FOTO KUNJUNGAN</th>
                            <th class="text-center align-middle">TANDA TANGAN & CAP INSTANSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${laporanHtml}
                    </tbody>
                </table>

                ${findingsHtml}

                <div class="row mt-5">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p>Surabaya,.........................</p>
                        <p>Guru Pembimbing,</p>
                        <div style="height: 100px;"></div>
                        <p><u>${response.guru}</u></p>
                        <p class="nip-container">NIP. ${response.nip || '-'}</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
    `;

                    // Create a hidden iframe for printing
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    document.body.appendChild(iframe);

                    // Write the content to the iframe
                    iframe.contentDocument.write(htmlContent);
                    iframe.contentDocument.close();

                    // Wait for images to load before printing
                    iframe.onload = function() {
                        setTimeout(function() {
                            iframe.contentWindow.print();
                            setTimeout(function() {
                                document.body.removeChild(iframe);
                            }, 1000);
                        }, 500);
                    };
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengambil data laporan.'
                    });
                }
            });
        });
        // Konfirmasi Hapus dengan SweetAlert
        $('.delete-button').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data jurusan akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection