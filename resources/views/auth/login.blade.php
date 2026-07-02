@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="g-card">
    <h4 class="mb-1">Selamat datang</h4>
    <p class="g-hint mb-4">Masuk ke akun Daily Plan Anda</p>

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   placeholder="nama@perusahaan.id"
                   value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control"
                   placeholder="••••••••" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Masuk</button>
        </div>
    </form>

    <p class="text-center mt-4 mb-0" style="font-size:13px;color:var(--n-ink-muted)">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </p>
</div>
@endsection
