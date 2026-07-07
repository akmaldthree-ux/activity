@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<div class="g-card g-card-wide">
    <h4 class="mb-1">Buat akun baru</h4>
    <p class="g-hint mb-4">Isi data diri Anda — akun akan aktif setelah disetujui manager</p>

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control"
                       placeholder="Nama sesuai KTP" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="col-12">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="email@perusahaan.com" value="{{ old('email') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 8 karakter" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control"
                       placeholder="Ulangi password" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Divisi</label>
                <select name="division_id" class="form-select" required>
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Role / Tingkatan</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    <option value="leader"   {{ old('role') === 'leader'   ? 'selected' : '' }}>Leader</option>
                    <option value="manager"  {{ old('role') === 'manager'  ? 'selected' : '' }}>Manager</option>
                    <option value="direksi"  {{ old('role') === 'direksi'  ? 'selected' : '' }}>Direksi</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Jabatan / Posisi</label>
                <input type="text" name="jabatan" class="form-control"
                       placeholder="Contoh: Shopee Advertiser" value="{{ old('jabatan') }}" required>
            </div>

            <div class="col-12 mt-1">
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Kirim Pendaftaran</button>
                </div>
            </div>
        </div>
    </form>

    <p class="text-center mt-4 mb-0" style="font-size:13px;color:var(--n-ink-muted)">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </p>
</div>
@endsection
