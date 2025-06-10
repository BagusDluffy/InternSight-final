@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Guru</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('guru.index') }}">Data Guru</a></li>
                        <li class="breadcrumb-item active">Edit Guru</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <form action="{{ route('guru.update', $guru) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Input untuk Nama Guru -->
                        <div class="form-group">
                            <label for="nama_guru">Nama Guru</label>
                            <input type="text" name="nama_guru" id="nama_guru" class="form-control @error('nama_guru') is-invalid @enderror" value="{{ old('nama_guru', $guru->nama_guru) }}" required>
                            @error('nama_guru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Input untuk Email -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $guru->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Input untuk NIP -->
                        <div class="form-group">
                            <label for="nip">NIP </label>
                            <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $guru->nip) }}">
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Input untuk No HP -->
                        <div class="form-group">
                            <label for="no_hp">Nomor Handphone </label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $guru->no_hp) }}">
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Input untuk Password Lama -->
                        <div class="form-group">
                            <label for="current_password">Password Lama</label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    name="current_password" 
                                    id="current_password" 
                                    class="form-control" 
                                    value="{{ Crypt::decryptString($guru->encrypted_password) }}" 
                                    readonly
                                >
                            </div>
                        </div>

                        <!-- Input untuk Password Baru -->
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    placeholder="Masukkan password baru jika ingin mengubah"
                                >
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2" id="simpan">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('guru.index') }}" class="btn btn-secondary" id="batal">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<style>

/* Untuk layar 1080p ke atas */
@media (min-width: 1920px) {
    .content-wrapper {
        max-width: 100%;
        margin-left: auto;
    }
    
    h1{
        font-weight: bold;
    }

    .card {
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Bayangan lembut */
    }

    .form-group label {
        font-size: 18px; /* Perbesar label input */
        font-weight: 600;
    }

    .form-control {
        font-size: 18px;
        padding: 10px;
        border-radius: 8px;
    }

    #simpan, #batal {
        font-size: 18px;
        padding: 12px 20px;
        border-radius: 8px;
    }

    .btn-primary {
        background-color: #1e5dd1;
        border-color: #1e5dd1;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn:hover {
        opacity: 0.9;
    }
}

</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle the icon
            this.innerHTML = type === 'password' 
                ? '<i class="fas fa-eye"></i>' 
                : '<i class="fas fa-eye-slash"></i>';
        });

        // Validasi real-time untuk input
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('invalid', function() {
                this.classList.add('is-invalid');
            });

            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>
@endpush
@endsection