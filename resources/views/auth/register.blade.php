@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<div class="card login-card border-0">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                <i class="bi bi-person-plus fs-4"></i>
            </div>
            <h4 class="fw-bold mb-0">Daftar Akun</h4>
            <p class="text-muted small">Isi data diri Anda untuk mendaftar</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" class="form-control" placeholder="Nama lengkap sesuai KTP"
                           value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="email@perusahaan.com"
                           value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Divisi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                    <select name="division_id" class="form-select" required>
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jabatan / Posisi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                    <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Staff IT, Sales Executive"
                           value="{{ old('jabatan') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Nomor HP / WhatsApp</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx"
                           value="{{ old('no_hp') }}" required>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                    <i class="bi bi-send me-1"></i>Kirim Pendaftaran
                </button>
            </div>
        </form>

        <p class="text-center text-muted small mb-0">
            Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
        </p>
    </div>
</div>

<p class="text-center text-white-50 small mt-3">
    <i class="bi bi-info-circle me-1"></i>Akun akan aktif setelah disetujui oleh manager divisi Anda.
</p>
@endsection
