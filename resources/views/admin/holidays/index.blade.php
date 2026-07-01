@extends('layouts.app')

@section('title', 'Hari Libur Nasional')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-x me-2"></i>Hari Libur Nasional</h5>
</div>

<div class="row g-4">
    {{-- Tambah Hari Libur --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-plus-circle me-1"></i>Tambah Hari Libur
            </div>
            <div class="card-body">
                <form action="{{ route('admin.holidays.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                               value="{{ old('date') }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nama Hari Libur <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Contoh: Hari Kemerdekaan RI" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i>Tambahkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar Hari Libur --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-list-ul me-1"></i>Daftar Hari Libur</span>
                <form class="d-flex gap-2">
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto">
                        @foreach($years as $y)
                            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body p-0">
                @if($holidays->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-check fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada hari libur untuk tahun {{ $year }}.
                    </div>
                @else
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Nama Hari Libur</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($holidays as $holiday)
                            <tr>
                                <td>{{ $holiday->date->translatedFormat('d M Y') }}</td>
                                <td>{{ $holiday->date->translatedFormat('l') }}</td>
                                <td>{{ $holiday->name }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST"
                                          onsubmit="return confirm('Hapus hari libur ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
