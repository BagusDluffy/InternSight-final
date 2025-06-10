@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Guru</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Data Guru</li>
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

        <!-- Add Guru Button and Import Button -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Left side: Add and Template Buttons -->
                    <div class="col-md-4">
                        <div class="d-flex">
                            <a href="{{ route('guru.create') }}" class="btn btn-primary mr-2">
                                <i class="fas fa-plus"></i> Tambah Guru
                            </a>
                            <a href="{{ route('guru.download-template') }}" class="btn btn-info">
                                <i class="fas fa-file-excel"></i> Download Template
                            </a>
                        </div>
                    </div>

                    <!-- Center: Import Section -->
                    <div class="col-md-4 text-center">
                        <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data" class="d-flex justify-content-center align-items-center">
                            @csrf
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                                    <label class="custom-file-label text-left" for="importFile">Pilih file Excel</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-upload" id></i> Import
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
                <form id="multiDeleteForm" action="{{ route('guru.multi-delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <table class="table table-bordered table-striped" id="guruTable">
                        <thead>
                            <tr class="text-center">
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>No</th>
                                <th>Nama Guru</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th>No HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guru as $key => $guruItem)
                            <tr class="text-center align-middle">
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="{{ $guruItem->id }}" class="checkbox-item">
                                </td>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $guruItem->nama_guru }}</td>
                                <td>{{ $guruItem->email }}</td>
                                <td>{{ $guruItem->nip ?? '-' }}</td>
                                <td>{{ $guruItem->no_hp ?? '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('guru.edit', $guruItem) }}" class="btn btn-warning btn-sm mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('guru.destroy', $guruItem) }}" method="POST" class="delete-form d-inline-block">
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
        /* Sesuaikan tinggi agar seragam */
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.28/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    // DataTable initialization
    const table = $('#guruTable').DataTable({
        responsive: true,
        autoWidth: false,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        order: [],
        columnDefs: [{
            orderable: false,
            targets: [0, 6] // Disable sorting for checkbox and action columns
        }]
    });

    // File input label update
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // Import file validation
    $('form[action*="guru.import"]').on('submit', function(e) {
        if ($('#importFile').val() === '') {
            e.preventDefault();
            Swal.fire('Pilih File!', 'Silakan pilih file sebelum mengimpor.', 'warning');
        }
    });

    // Checkbox select all logic
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
            title: 'Hapus Guru Terpilih',
            text: `Anda akan menghapus ${selectedCount} guru. Apakah Anda yakin?`,
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
            text: 'Data guru akan dihapus permanen!',
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
            title: 'Hapus Semua Guru',
            text: 'Anda akan menghapus SEMUA data guru. Aksi ini tidak dapat dibatalkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('guru.delete-all') }}",
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Semua data guru telah dihapus.', 'success')
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