<x-mail::message>
# Pengingat Report Sore

Halo, **{{ $user->name }}**!

Kamu belum mengisi **report sore** untuk hari ini, **{{ $date->translatedFormat('l, d F Y') }}**.

Jangan lupa isi sebelum jam **20:00 WIB** ya!

<x-mail::button :url="config('app.url') . '/kalender/' . $date->format('Y-m-d')" color="success">
Isi Report Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
