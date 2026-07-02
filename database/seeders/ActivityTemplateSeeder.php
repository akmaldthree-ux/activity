<?php

namespace Database\Seeders;

use App\Models\ActivityTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            'andi@dailyplan.id' => [
                ['description' => 'Daily standup meeting tim IT', 'priority' => 'sedang'],
                ['description' => 'Code review dan merge PR dari rekan', 'priority' => 'sedang'],
                ['description' => 'Implementasi fitur sesuai sprint backlog', 'priority' => 'tinggi'],
                ['description' => 'Update dokumentasi teknis', 'priority' => 'rendah'],
            ],
            'rina@dailyplan.id' => [
                ['description' => 'Pengembangan REST API endpoint baru', 'priority' => 'tinggi'],
                ['description' => 'Optimasi performa query database', 'priority' => 'sedang'],
                ['description' => 'Unit testing modul yang dikerjakan', 'priority' => 'sedang'],
            ],
            'fajar@dailyplan.id' => [
                ['description' => 'Monitoring uptime server produksi', 'priority' => 'tinggi'],
                ['description' => 'Update konfigurasi server dan deployment', 'priority' => 'sedang'],
            ],
            'deni@dailyplan.id' => [
                ['description' => 'Buat konten media sosial harian', 'priority' => 'tinggi'],
                ['description' => 'Monitoring engagement dan statistik iklan', 'priority' => 'sedang'],
                ['description' => 'Rapat koordinasi tim marketing', 'priority' => 'sedang'],
                ['description' => 'Laporan mingguan performa kampanye', 'priority' => 'tinggi'],
            ],
            'lestari@dailyplan.id' => [
                ['description' => 'Produksi konten Instagram dan TikTok', 'priority' => 'tinggi'],
                ['description' => 'Riset tren konten terkini', 'priority' => 'sedang'],
                ['description' => 'Editing foto dan video untuk posting', 'priority' => 'sedang'],
            ],
            'mira@dailyplan.id' => [
                ['description' => 'Koordinasi jadwal pengiriman harian', 'priority' => 'tinggi'],
                ['description' => 'Update laporan stok barang masuk/keluar', 'priority' => 'sedang'],
                ['description' => 'Follow up status pengiriman ke klien', 'priority' => 'sedang'],
            ],
            'nita@dailyplan.id' => [
                ['description' => 'Review lamaran masuk dan screening kandidat', 'priority' => 'tinggi'],
                ['description' => 'Update data absensi dan kehadiran karyawan', 'priority' => 'sedang'],
                ['description' => 'Koordinasi jadwal pelatihan karyawan', 'priority' => 'sedang'],
                ['description' => 'Proses administrasi kepegawaian', 'priority' => 'rendah'],
            ],
            'bambang@dailyplan.id' => [
                ['description' => 'Jadwal dan proses wawancara kandidat', 'priority' => 'tinggi'],
                ['description' => 'Update job posting di platform rekrutmen', 'priority' => 'sedang'],
            ],
            'rudi@dailyplan.id' => [
                ['description' => 'Input dan verifikasi transaksi harian', 'priority' => 'tinggi'],
                ['description' => 'Rekonsiliasi laporan keuangan', 'priority' => 'tinggi'],
                ['description' => 'Koordinasi pembayaran vendor dan tagihan', 'priority' => 'sedang'],
                ['description' => 'Update laporan arus kas', 'priority' => 'sedang'],
            ],
            'citra@dailyplan.id' => [
                ['description' => 'Analisis laporan keuangan bulanan', 'priority' => 'tinggi'],
                ['description' => 'Review anggaran vs realisasi departemen', 'priority' => 'sedang'],
                ['description' => 'Persiapan bahan presentasi keuangan', 'priority' => 'sedang'],
            ],
        ];

        foreach ($templates as $email => $userTemplates) {
            $userId = User::where('email', $email)->value('id');
            if (!$userId) continue;

            foreach ($userTemplates as $t) {
                ActivityTemplate::firstOrCreate(
                    ['user_id' => $userId, 'description' => $t['description']],
                    ['priority' => $t['priority']]
                );
            }
        }
    }
}
