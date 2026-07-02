@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-circle me-2"></i>Profil Saya</h5>
</div>

<div class="row g-4">
    {{-- Edit Profil --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-pencil-square me-1"></i>Edit Profil
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    {{-- Foto profil --}}
                    <div class="text-center mb-4">
                        @if($user->photo)
                            <img src="{{ Storage::url($user->photo) }}" alt="Foto Profil"
                                 class="rounded-circle object-fit-cover mb-2" style="width:96px;height:96px;object-fit:cover;">
                        @else
                            <div class="n-initials mb-2" style="width:96px;height:96px;font-size:2rem;background:var(--n-primary);display:inline-flex">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <label class="btn btn-sm btn-outline-secondary" for="photo">
                                <i class="bi bi-camera me-1"></i>Ganti Foto
                            </label>
                            <input type="file" id="photo" name="photo" class="d-none" accept="image/*"
                                   onchange="previewPhoto(this)">
                            @error('photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                        <div class="form-text">Email tidak dapat diubah.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                               value="{{ old('jabatan', $user->jabatan) }}" placeholder="Contoh: Frontend Developer">
                        @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxxx">
                        @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-shield-lock me-1"></i>Ganti Password
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="cur_pw"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Password lama" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePw('cur_pw')">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="new_pw"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 karakter" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePw('new_pw')">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="conf_pw"
                                   class="form-control" placeholder="Ulangi password baru" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePw('conf_pw')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-key me-1"></i>Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Akun --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-info-circle me-1"></i>Informasi Akun
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Role</td>
                        <td><span class="badge bg-primary">{{ $user->roleLabel() }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Divisi</td>
                        <td>{{ $user->division?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td><span class="badge {{ $user->statusBadgeClass() }}">{{ $user->statusLabel() }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bergabung</td>
                        <td>{{ $user->created_at->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const existing = document.querySelector('img.rounded-circle');
        const placeholder = document.querySelector('.bg-primary.rounded-circle');
        if (existing) {
            existing.src = e.target.result;
        } else if (placeholder) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'rounded-circle object-fit-cover mb-2';
            img.style = 'width:96px;height:96px;object-fit:cover;';
            placeholder.replaceWith(img);
        }
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
