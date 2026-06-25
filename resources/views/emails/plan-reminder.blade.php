<x-mail::message>
# Pengingat Plan Pagi

Halo, **{{ $user->name }}**!

Kamu belum mengisi **plan pagi** untuk hari ini, **{{ $date->translatedFormat('l, d F Y') }}**.

Jangan lupa isi sebelum jam **09:00 WIB** ya!

<x-mail::button :url="config('app.url') . '/kalender/' . $date->format('Y-m-d')" color="primary">
Isi Plan Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
