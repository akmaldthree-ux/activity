<x-mail::message>
# Ada Pendaftar Baru

Halo, **{{ $manager->name }}**!

Ada karyawan baru yang mendaftar dan menunggu persetujuan Anda:

<x-mail::table>
| Info | Detail |
|:-----|:-------|
| **Nama** | {{ $applicant->name }} |
| **Email** | {{ $applicant->email }} |
| **Jabatan** | {{ $applicant->jabatan }} |
| **No. HP** | {{ $applicant->no_hp }} |
| **Divisi** | {{ $applicant->division?->name ?? '-' }} |
</x-mail::table>

Silakan buka halaman persetujuan untuk menyetujui atau menolak pendaftaran ini.

<x-mail::button :url="config('app.url') . '/approval'" color="primary">
Buka Halaman Persetujuan
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
