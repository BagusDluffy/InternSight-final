@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Magang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('magang.index') }}">Magang</a></li>
                        <li class="breadcrumb-item active">Edit Magang</li>
                    </ol>
                </div>
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('magang.update', $magang->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Informasi yang tidak bisa diubah --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nama Guru Pembimbing</label>
                                    <input type="text" class="form-control" value="{{ $magang->guru->nama_guru }}" readonly>
                                    <input type="hidden" name="guru_id" value="{{ $magang->guru_id }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>DUDIKA</label>
                                    <input type="text" class="form-control" value="{{ $magang->dudika->dudika }}" readonly>
                                    <input type="hidden" name="dudika_id" value="{{ $magang->dudika_id }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Periode</label>
                                    <input type="text" class="form-control" value="{{ $magang->periode }}" readonly>
                                    <input type="hidden" name="periode" value="{{ $magang->periode }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="jurusan_id">Jurusan</label>
                            <select class="form-control @error('jurusan_id') is-invalid @enderror" id="jurusan_id" name="jurusan_id">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusan as $item)
                                <option value="{{ $item->id }}"
                                    {{ $magang->jurusan_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->jurusan }}
                                </option>
                                @endforeach
                            </select>
                            @error('jurusan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kelas">Kelas</label>
                            <select class="form-control @error('kelas') is-invalid @enderror" id="kelas" name="kelas">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k->kelas }}"
                                    {{ $magang->kelas == $k->kelas ? 'selected' : '' }}>
                                    {{ $k->kelas }}
                                </option>
                                @endforeach
                            </select>
                            @error('kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="murid-container">
                            @foreach($magang->murid as $index => $murid)
                            <div class="murid-entry form-group">
                                <label>Nama Murid</label>
                                <div class="input-group">
                                    <select class="form-control murid-select @error('murid_id.'.$index) is-invalid @enderror" name="murid_id[]">
                                        <option value="">Pilih Murid</option>
                                        @foreach($muridOptions as $option)
                                        <option value="{{ $option->id }}"
                                            {{ $murid->id == $option->id ? 'selected' : '' }}>
                                            {{ $option->nama_murid }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        @if($index === 0)
                                        <button type="button" class="btn btn-success add-murid">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-danger remove-murid">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @error('murid_id.'.$index)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('magang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jurusanSelect = document.getElementById('jurusan_id');
        const kelasSelect = document.getElementById('kelas');
        const muridContainer = document.getElementById('murid-container');
        const addMuridButton = document.querySelector('.add-murid');
        let selectedMuridIds = [];

        function getMurid(jurusanId, kelas, selectElement) {
            if (!jurusanId || !kelas) {
                selectElement.innerHTML = '<option value="">Pilih Murid</option>';
                return;
            }

            fetch(`/get-murid?jurusan_id=${jurusanId}&kelas=${kelas}`)
                .then(response => response.json())
                .then(response => {
                    const currentValue = selectElement.value;
                    selectElement.innerHTML = '<option value="">Pilih Murid</option>';

                    // Filter out selected murid, but keep current selection
                    const availableMurid = response.data.filter(murid =>
                        !selectedMuridIds.includes(murid.id) || murid.id == currentValue
                    );

                    availableMurid.forEach(murid => {
                        const option = new Option(murid.nama_murid, murid.id);
                        if (murid.id == currentValue) {
                            option.selected = true;
                        }
                        selectElement.add(option);
                    });

                    updateSelectedMuridIds();
                })
                .catch(error => {
                    console.error('Error:', error);
                    selectElement.innerHTML = '<option value="">Gagal memuat murid</option>';
                });
        }

        function updateSelectedMuridIds() {
            selectedMuridIds = Array.from(
                    muridContainer.querySelectorAll('.murid-select')
                )
                .map(select => select.value)
                .filter(value => value !== '');
        }

        // Event listeners for jurusan and kelas
        jurusanSelect.addEventListener('change', function() {
            const muridSelects = muridContainer.querySelectorAll('.murid-select');
            muridSelects.forEach(select => {
                getMurid(this.value, kelasSelect.value, select);
            });
        });

        kelasSelect.addEventListener('change', function() {
            const muridSelects = muridContainer.querySelectorAll('.murid-select');
            muridSelects.forEach(select => {
                getMurid(jurusanSelect.value, this.value, select);
            });
        });

        // Add murid button handler
        addMuridButton.addEventListener('click', function() {
            const newMuridEntry = document.createElement('div');
            newMuridEntry.classList.add('murid-entry', 'form-group');
            newMuridEntry.innerHTML = `
            <label>Nama Murid</label>
            <div class="input-group">
                <select class="form-control murid-select" name="murid_id[]">
                    <option value="">Pilih Murid</option>
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-murid">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        `;

            muridContainer.appendChild(newMuridEntry);

            const newSelect = newMuridEntry.querySelector('.murid-select');
            getMurid(jurusanSelect.value, kelasSelect.value, newSelect);
        });

        // Remove murid handler
        muridContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-murid')) {
                e.target.closest('.murid-entry').remove();
                updateSelectedMuridIds();
            }
        });

        // Initial setup
        updateSelectedMuridIds();
    });
</script>
@endpush
@endsection