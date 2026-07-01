<x-mail::message>
# Akun Anda Disetujui! 🎉

Halo, **{{ $user->name }}**!

Selamat! Pendaftaran akun Anda di **{{ config('app.name') }}** telah **disetujui** oleh manager.

Anda sekarang sudah bisa login dan mulai menggunakan aplikasi.

<x-mail::button :url="config('app.url') . '/login'" color="success">
Login Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
