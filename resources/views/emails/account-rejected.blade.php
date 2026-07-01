<x-mail::message>
# Informasi Pendaftaran Akun

Halo, **{{ $user->name }}**!

Mohon maaf, pendaftaran akun Anda di **{{ config('app.name') }}** **tidak dapat disetujui** saat ini.

@if($user->rejection_reason)
**Alasan:** {{ $user->rejection_reason }}
@endif

Jika Anda merasa ini adalah kesalahan, silakan hubungi manager atau admin perusahaan Anda secara langsung.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
