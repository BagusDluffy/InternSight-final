@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Murid</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Data Murid</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
        </div>

        <!-- Add Murid Button and Import Button -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Left side: Add and Template Buttons -->
                    <div class="col-md-4">
                        <div class="d-flex">
                            <a href="{{ route('murid.create') }}" class="btn btn-primary mr-2">
                                <i class="fas fa-plus"></i> Tambah Murid
                            </a>
                            <a href="{{ route('murid.download-template') }}" class="btn btn-info">
                                <i class="fas fa-file-excel"></i> Download Template
                            </a>
                        </div>
                    </div>

                    <!-- Center: Import Section -->
                    <div class="col-md-4 text-center">
                        <form action="{{ route('murid.import') }}" method="POST" enctype="multipart/form-data" class="d-flex justify-content-center align-items-center">
                            @csrf
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                                    <label class="custom-file-label text-left" for="importFile">Pilih file Excel</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-upload"></i> Import
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Right side: Delete Buttons -->
                    <div class="col-md-4 text-right">
                        <div class="btn-group" role="group">
                            <button id="deleteSelected" class="btn btn-danger mr-2" disabled>
                                <i class="fas fa-trash"></i> Hapus Terpilih
                            </button>
                            <button id="deleteAll" class="btn btn-danger">
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
                <form id="multiDeleteForm" action="{{ route('murid.multi-delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <table class="table table-bordered table-striped" id="muridTable">
                        <thead>
                            <tr class="text-center">
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>No</th>
                                <th>Nama Murid</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($murid as $key => $muridItem)
                            <tr class="text-center align-middle">
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="{{ $muridItem->id }}" class="checkbox-item">
                                </td>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $muridItem->nama_murid }}</td>
                                <td>{{ $muridItem->nis }}</td>
                                <td>{{ $muridItem->kelas }}</td>
                                <td>{{ $muridItem->jurusan->jurusan ?? '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('murid.edit', $muridItem) }}" class="btn btn-warning btn-sm mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('murid.destroy', $muridItem) }}" method="POST" class="delete-form d-inline-block">
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
        h1 {
            font-size: 32px !important;
            font-weight: bold !important;
        }

        table th,
        table td {
            font-size: 18px !important;
            padding: 12px !important;
        }

        .form-group label {
            font-size: 18px !important;
            font-weight: 600 !important;
        }

        .form-control {
            font-size: 18px !important;
            padding: 10px !important;
            border-radius: 8px !important;
        }

        .custom-file-input,
        .custom-file-label {
            font-size: 18px !important;
            padding: 10px !important;
        }

        .custom-file-label::after {
            padding: 10px 15px !important;
        }

        .input-group {
            font-size: 16px !important;
        }

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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.28/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#muridTable').DataTable({
            responsive: true,
            autoWidth: false,
            lengthChange: true,
            searching: true,
            ordering: true,
            order: [],
            info: true,
            columnDefs: [{
                orderable: false,
                targets: [0, 6] // Disable sorting for checkbox and action columns
            }]
        });

        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        });

        $('form[action*="murid.import"]').on('submit', function(e) {
            if ($('#importFile').val() === '') {
                e.preventDefault();
                Swal.fire('Pilih File!', 'Silakan pilih file sebelum mengimpor.', 'warning');
            }
        });

        $('#selectAll').on('change', function() {
            $('.checkbox-item').prop('checked', this.checked).change();
        });

        $(document).on('change', '.checkbox-item', function() {
            $('#selectAll').prop('checked', $('.checkbox-item:checked').length === $('.checkbox-item').length);
            $('#deleteSelected').prop('disabled', $('.checkbox-item:checked').length === 0);
        });

        $('#deleteSelected').on('click', function() {
            const selectedCount = $('.checkbox-item:checked').length;
            Swal.fire({
                title: 'Hapus Murid Terpilih',
                text: `Anda akan menghapus ${selectedCount} murid. Apakah Anda yakin?`,
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

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Data murid akan dihapus permanen!',
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

        $('#deleteAll').on('click', function() {
            Swal.fire({
                title: 'Hapus Semua Murid',
                text: 'Anda akan menghapus SEMUA data murid. Aksi ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('murid.delete-all') }}",
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', 'Semua data murid telah dihapus.', 'success')
                                .then(() => location.reload());
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection