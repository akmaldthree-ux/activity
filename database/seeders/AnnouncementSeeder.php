<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Division;
use App\Models\InAppNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $adminId  = User::where('email', 'admin@dailyplan.id')->value('id');
        $mgrItId  = User::where('email', 'manager.it@dailyplan.id')->value('id');
        $mgrMktId = User::where('email', 'manager.mkt@dailyplan.id')->value('id');
        $mgrHrdId = User::where('email', 'manager.hrd@dailyplan.id')->value('id');

        $itDivId  = Division::where('name', 'IT & Pengembangan')->value('id');
        $mktDivId = Division::where('name', 'Marketing')->value('id');

        $announcements = [
            // Global announcements from admin
            [
                'user_id'      => $adminId,
                'title'        => 'Selamat Datang di Sistem Daily Plan DSM Corp',
                'body'         => "Sistem Daily Plan resmi digunakan mulai hari ini. Semua karyawan diwajibkan mengisi rencana harian (plan) setiap pagi sebelum pukul 09.00 WIB dan laporan realisasi (report) setiap sore sebelum pukul 20.00 WIB.\n\nPanduan penggunaan sistem tersedia di menu bantuan. Hubungi IT Support jika mengalami kendala teknis.",
                'division_id'  => null,
                'published_at' => '2026-05-04 07:00:00',
            ],
            [
                'user_id'      => $adminId,
                'title'        => 'Libur Nasional: Kenaikan Yesus Kristus 14 Mei 2026',
                'body'         => "Mengingatkan seluruh karyawan bahwa tanggal 14 Mei 2026 adalah hari libur nasional dalam rangka Kenaikan Yesus Kristus. Tidak ada kewajiban pengisian Daily Plan pada tanggal tersebut.\n\nMohon koordinasikan pekerjaan yang mendesak dengan rekan atau atasan Anda sebelum hari libur.",
                'division_id'  => null,
                'published_at' => '2026-05-11 10:00:00',
            ],
            [
                'user_id'      => $adminId,
                'title'        => 'Evaluasi Kepatuhan Daily Plan Bulan Mei 2026',
                'body'         => "Berdasarkan rekap bulan Mei 2026, rata-rata kepatuhan pengisian Daily Plan perusahaan mencapai 72%. Ini merupakan peningkatan dari bulan April sebesar 65%.\n\nDivisi IT dan HRD mencatat performa terbaik dengan kepatuhan di atas 80%. Apresiasi setinggi-tingginya untuk semua karyawan yang konsisten.\n\nUntuk bulan Juni, target perusahaan adalah mencapai kepatuhan 80%. Mari bersama-sama kita wujudkan.",
                'division_id'  => null,
                'published_at' => '2026-06-02 08:00:00',
            ],
            [
                'user_id'      => $adminId,
                'title'        => 'Cuti Bersama Idul Adha: 26-27 Juni 2026',
                'body'         => "Diberitahukan kepada seluruh karyawan bahwa tanggal 26-27 Juni 2026 ditetapkan sebagai cuti bersama Hari Raya Idul Adha 1447 H. Kegiatan operasional perusahaan diliburkan pada tanggal tersebut.\n\nKaryawan yang memiliki tugas mendesak harap berkoordinasi dengan manager masing-masing. Selamat Hari Raya Idul Adha bagi yang merayakan.",
                'division_id'  => null,
                'published_at' => '2026-06-22 09:00:00',
            ],
            [
                'user_id'      => $adminId,
                'title'        => 'Pengumuman: Review KPI Pertengahan Tahun Juli 2026',
                'body'         => "Sehubungan dengan evaluasi kinerja pertengahan tahun, seluruh divisi diminta untuk menyiapkan laporan realisasi target H1 2026 paling lambat tanggal 10 Juli 2026.\n\nFormat laporan dapat diunduh dari folder shared. Manager setiap divisi akan menjadwalkan sesi review dengan direksi pada minggu kedua Juli.\n\nPastikan data Daily Plan bulan Mei dan Juni sudah terisi lengkap sebagai bahan evaluasi.",
                'division_id'  => null,
                'published_at' => '2026-07-01 08:00:00',
            ],
            // Division-specific announcements
            [
                'user_id'      => $mgrItId,
                'title'        => '[IT] Sprint Review & Planning — Minggu ke-3 Juni',
                'body'         => "Reminder untuk seluruh tim IT: Sprint Review dan Planning untuk minggu ke-3 Juni akan dilaksanakan pada Senin, 15 Juni 2026 pukul 09.00 WIB di Meeting Room Lantai 3.\n\nMohon siapkan update progress masing-masing task. Bagi yang remote, join via link Google Meet yang sudah dikirimkan ke email.",
                'division_id'  => $itDivId,
                'published_at' => '2026-06-12 15:00:00',
            ],
            [
                'user_id'      => $mgrMktId,
                'title'        => '[Marketing] Target Kampanye Digital Q3 2026',
                'body'         => "Tim Marketing yang terhormat,\n\nBerikut target kampanye digital Q3 2026 yang telah disetujui manajemen:\n• Reach total: 500.000 akun\n• Engagement rate: min 3.5%\n• Konversi iklan: min 2%\n• Leads baru: 200 per bulan\n\nStrategi detail akan dibahas dalam rapat tim pada Kamis, 2 Juli 2026. Mohon hadir tepat waktu.",
                'division_id'  => $mktDivId,
                'published_at' => '2026-06-29 11:00:00',
            ],
            [
                'user_id'      => $mgrHrdId,
                'title'        => '[HRD] Program Pelatihan Leadership Q3 2026',
                'body'         => "Informasi untuk seluruh karyawan: Program pelatihan Leadership Development Q3 2026 akan dimulai pada 20 Juli 2026. Pelatihan ini ditujukan untuk karyawan yang sudah bekerja minimal 1 tahun.\n\nDaftarkan diri melalui sistem HR sebelum 15 Juli 2026. Kuota terbatas untuk 20 peserta. Informasi lebih lanjut hubungi tim HRD.",
                'division_id'  => null,
                'published_at' => '2026-07-01 10:00:00',
            ],
        ];

        foreach ($announcements as $data) {
            $ann = Announcement::create($data);

            // Create in-app notifications for relevant users
            $this->notifyUsers($ann);
        }
    }

    private function notifyUsers(Announcement $ann): void
    {
        $query = User::where('is_active', true)->where('role', 'karyawan');
        if ($ann->division_id) {
            $query->where('division_id', $ann->division_id);
        }

        $users = $query->get();
        foreach ($users as $user) {
            InAppNotification::create([
                'user_id'    => $user->id,
                'type'       => 'announcement',
                'title'      => 'Pengumuman baru: ' . $ann->title,
                'body'       => mb_substr(strip_tags($ann->body), 0, 100) . '...',
                'url'        => '/pengumuman/' . $ann->id,
                'read_at'    => mt_rand(0, 1) ? now()->subHours(mt_rand(1, 72)) : null,
                'created_at' => $ann->published_at,
                'updated_at' => $ann->published_at,
            ]);
        }
    }
}
