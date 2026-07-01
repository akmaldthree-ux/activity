@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="card login-card border-0">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                <i class="bi bi-calendar-check fs-4"></i>
            </div>
            <h4 class="fw-bold mb-0">Daily Plan</h4>
            <p class="text-muted small">Masuk ke akun Anda</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="nama@perusahaan.id"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                </button>
            </div>
        </form>

        <p class="text-center text-muted small mt-3 mb-0">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </p>
    </div>
</div>
@endsection
