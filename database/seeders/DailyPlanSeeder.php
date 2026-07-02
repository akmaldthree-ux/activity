<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Models\Goal;
use App\Models\InAppNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailyPlanSeeder extends Seeder
{
    // Activity pool per division
    private array $pool = [
        'IT & Pengembangan' => [
            ['desc' => 'Implementasi fitur autentikasi dua faktor',         'tag' => 'Coding',   'priority' => 'tinggi'],
            ['desc' => 'Code review Pull Request modul laporan',            'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Sprint planning meeting iterasi ke-12',             'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Fix bug halaman dashboard tidak load di mobile',    'tag' => 'Coding',   'priority' => 'tinggi'],
            ['desc' => 'Deploy aplikasi ke server staging',                 'tag' => 'Coding',   'priority' => 'sedang'],
            ['desc' => 'Dokumentasi REST API endpoint baru',               'tag' => 'Laporan',  'priority' => 'rendah'],
            ['desc' => 'Optimasi query SQL laporan bulanan',               'tag' => 'Coding',   'priority' => 'sedang'],
            ['desc' => 'Troubleshooting error 500 di server produksi',     'tag' => 'Support',  'priority' => 'tinggi'],
            ['desc' => 'Setup pipeline CI/CD di GitLab',                   'tag' => 'Coding',   'priority' => 'sedang'],
            ['desc' => 'Rapat evaluasi sprint minggu lalu',                'tag' => 'Meeting',  'priority' => 'sedang'],
            ['desc' => 'Refactoring modul manajemen pengguna',             'tag' => 'Coding',   'priority' => 'rendah'],
            ['desc' => 'Testing fitur notifikasi email otomatis',          'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Update library dependencies keamanan',             'tag' => 'Coding',   'priority' => 'tinggi'],
            ['desc' => 'Koordinasi teknis dengan tim produk',              'tag' => 'Meeting',  'priority' => 'sedang'],
        ],
        'Marketing' => [
            ['desc' => 'Membuat konten Instagram feed dan stories',         'tag' => 'Desain',   'priority' => 'tinggi'],
            ['desc' => 'Rapat koordinasi kampanye digital Q3',             'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Analisis performa iklan Facebook Ads bulan lalu',  'tag' => 'Laporan',  'priority' => 'sedang'],
            ['desc' => 'Desain banner promosi produk baru',                'tag' => 'Desain',   'priority' => 'sedang'],
            ['desc' => 'Laporan bulanan kampanye media sosial',            'tag' => 'Laporan',  'priority' => 'tinggi'],
            ['desc' => 'Survei kepuasan pelanggan Q2',                     'tag' => 'Laporan',  'priority' => 'sedang'],
            ['desc' => 'Koordinasi kolaborasi dengan influencer',          'tag' => 'Meeting',  'priority' => 'sedang'],
            ['desc' => 'Update konten halaman website perusahaan',         'tag' => 'Desain',   'priority' => 'rendah'],
            ['desc' => 'Persiapan materi untuk pameran produk',            'tag' => 'Desain',   'priority' => 'tinggi'],
            ['desc' => 'Monitoring engagement dan reach media sosial',     'tag' => 'Laporan',  'priority' => 'rendah'],
            ['desc' => 'Membuat email newsletter mingguan',                'tag' => 'Desain',   'priority' => 'sedang'],
            ['desc' => 'Riset kompetitor untuk strategi Q3',               'tag' => 'Laporan',  'priority' => 'sedang'],
        ],
        'Operasional' => [
            ['desc' => 'Koordinasi pengiriman ke gudang Surabaya',         'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Rapat evaluasi performa vendor logistik',          'tag' => 'Meeting',  'priority' => 'sedang'],
            ['desc' => 'Pembuatan laporan stok barang bulanan',            'tag' => 'Laporan',  'priority' => 'tinggi'],
            ['desc' => 'Monitoring status pengiriman order klien',         'tag' => 'Support',  'priority' => 'sedang'],
            ['desc' => 'Evaluasi dan revisi SOP pengiriman barang',        'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Input data penerimaan barang di sistem gudang',    'tag' => 'Laporan',  'priority' => 'rendah'],
            ['desc' => 'Pengecekan dan perawatan armada kendaraan',        'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Koordinasi jadwal pengiriman minggu depan',        'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Laporan kerusakan barang dan klaim asuransi',      'tag' => 'Laporan',  'priority' => 'sedang'],
            ['desc' => 'Negosiasi tarif dengan calon vendor baru',         'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Pengecekan kelengkapan dokumen pengiriman',        'tag' => 'Review',   'priority' => 'rendah'],
        ],
        'HRD' => [
            ['desc' => 'Interview kandidat posisi Senior Developer',       'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Pelatihan onboarding karyawan baru batch Juli',    'tag' => 'Training', 'priority' => 'tinggi'],
            ['desc' => 'Pembuatan laporan absensi bulan Juni',             'tag' => 'Laporan',  'priority' => 'sedang'],
            ['desc' => 'Koordinasi benefit dan tunjangan karyawan',        'tag' => 'Meeting',  'priority' => 'sedang'],
            ['desc' => 'Evaluasi efektivitas program pelatihan Q2',        'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Update database profil karyawan baru',             'tag' => 'Laporan',  'priority' => 'rendah'],
            ['desc' => 'Rapat penilaian kinerja tahunan bersama direksi', 'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Persiapan materi training leadership level manager','tag' => 'Training', 'priority' => 'sedang'],
            ['desc' => 'Review dan update kebijakan cuti karyawan',        'tag' => 'Review',   'priority' => 'rendah'],
            ['desc' => 'Rekrutmen via LinkedIn dan Jobstreet',             'tag' => 'Laporan',  'priority' => 'sedang'],
            ['desc' => 'Workshop team building divisi Q3',                 'tag' => 'Training', 'priority' => 'sedang'],
        ],
        'Keuangan' => [
            ['desc' => 'Rekonsiliasi laporan keuangan bulan Juni',         'tag' => 'Laporan',  'priority' => 'tinggi'],
            ['desc' => 'Review anggaran belanja operasional Q3',           'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Rapat dengan auditor eksternal PWC',               'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Pembuatan laporan pajak PPN bulan Juni',           'tag' => 'Laporan',  'priority' => 'tinggi'],
            ['desc' => 'Analisis cashflow dan proyeksi keuangan Q3',       'tag' => 'Laporan',  'priority' => 'sedang'],
            ['desc' => 'Persiapan laporan keuangan untuk rapat direksi',   'tag' => 'Laporan',  'priority' => 'tinggi'],
            ['desc' => 'Input dan verifikasi faktur pembelian vendor',     'tag' => 'Laporan',  'priority' => 'rendah'],
            ['desc' => 'Review kontrak kerja sama dengan klien baru',      'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Koordinasi proses pembayaran gaji karyawan',       'tag' => 'Meeting',  'priority' => 'tinggi'],
            ['desc' => 'Verifikasi laporan pengeluaran antar departemen',  'tag' => 'Review',   'priority' => 'sedang'],
            ['desc' => 'Audit dokumen keuangan periode Q1',                'tag' => 'Review',   'priority' => 'sedang'],
        ],
    ];

    private array $feedbackComments = [
        5 => [
            'Excellent! Semua aktivitas selesai dengan sempurna. Pertahankan konsistensi ini.',
            'Luar biasa! Kerja keras dan komitmen sangat terlihat. Terus tingkatkan.',
            'Performa sangat memuaskan hari ini. Ini adalah contoh yang baik untuk tim.',
        ],
        4 => [
            'Bagus! Progress sangat baik, hanya ada sedikit poin kecil yang perlu diperhatikan.',
            'Kinerja hari ini memuaskan. Hampir sempurna, pertahankan semangat kerja ini.',
            'Kerja keras hari ini sangat terlihat. Satu langkah lagi untuk sempurna.',
        ],
        3 => [
            'Cukup baik, namun beberapa target belum tercapai secara optimal. Tingkatkan lagi.',
            'Progress ada tapi masih bisa lebih baik. Fokus pada prioritas utama besok.',
            'Standar hari ini. Mohon lebih fokus dan manajemen waktu lebih baik.',
        ],
        2 => [
            'Perlu lebih banyak usaha. Beberapa tugas penting tidak selesai hari ini.',
            'Kinerja hari ini di bawah ekspektasi. Mohon evaluasi dan perbaiki segera.',
            'Masih jauh dari target divisi. Saya ingin diskusi langsung besok pagi.',
        ],
        1 => [
            'Sangat mengkhawatirkan. Hampir tidak ada task yang selesai. Perlu perhatian serius.',
            'Perlu konsultasi dan evaluasi menyeluruh. Tolong hubungi saya hari ini.',
        ],
    ];

    private array $goalsByDivision = [
        'IT & Pengembangan' => [
            ['desc' => 'Selesaikan minimum 2 fitur dari sprint backlog', 'target' => 'Min. 2 task selesai'],
            ['desc' => 'Zero critical bug di modul yang dikerjakan',     'target' => '0 critical bug'],
            ['desc' => 'Dokumentasi teknis terupdate',                   'target' => '1 dokumen selesai'],
            ['desc' => 'Code coverage test minimal 80%',                 'target' => '80% coverage'],
        ],
        'Marketing' => [
            ['desc' => 'Engagement rate konten minimal 3%',        'target' => 'ER ≥ 3%'],
            ['desc' => 'Selesaikan semua jadwal konten minggu ini', 'target' => '100% terjadwal'],
            ['desc' => 'Laporan kampanye terkirim ke manager',      'target' => 'Kirim sebelum 17:00'],
            ['desc' => 'Reach posting minimal 5.000 akun',         'target' => '≥ 5.000 reach'],
        ],
        'Operasional' => [
            ['desc' => 'Zero keterlambatan pengiriman hari ini',    'target' => '0 keterlambatan'],
            ['desc' => 'Laporan stok akurat 100%',                  'target' => 'Akurasi 100%'],
            ['desc' => 'Semua dokumen pengiriman lengkap',          'target' => 'Lengkap sebelum jam 15:00'],
        ],
        'HRD' => [
            ['desc' => 'Proses rekrutmen berjalan sesuai jadwal',   'target' => 'On schedule'],
            ['desc' => 'Laporan absensi akurat dan terkirim',        'target' => 'Kirim sebelum jam 10:00'],
            ['desc' => 'Semua peserta pelatihan hadir',             'target' => 'Kehadiran 100%'],
        ],
        'Keuangan' => [
            ['desc' => 'Rekonsiliasi selesai tanpa selisih',         'target' => 'Selisih = 0'],
            ['desc' => 'Laporan keuangan terkirim tepat waktu',      'target' => 'Kirim sebelum deadline'],
            ['desc' => 'Semua faktur terverifikasi',                 'target' => 'Verifikasi 100%'],
        ],
    ];

    private array $replies = [
        'Terima kasih atas feedbacknya, Pak/Bu. Akan saya perbaiki mulai besok.',
        'Siap, akan saya usahakan lebih baik lagi. Terima kasih atas arahannya.',
        'Terima kasih, sudah saya catat untuk perbaikan ke depannya.',
        'Mohon maaf atas kekurangannya hari ini. Akan lebih fokus besok.',
        'Terima kasih atas apresiasinya. Akan terus saya tingkatkan.',
        'Baik Pak/Bu, poin tersebut akan saya jadikan prioritas besok.',
    ];

    private array $insights = [
        'Hari ini berjalan lancar, semua tugas selesai sesuai rencana.',
        'Ada hambatan teknis di pagi hari namun berhasil diselesaikan sore hari.',
        'Rapat berlangsung lebih lama dari rencana sehingga beberapa tugas tertunda.',
        'Produktif hari ini, berhasil menyelesaikan lebih dari target yang direncanakan.',
        'Perlu koordinasi lebih intensif dengan tim lain untuk kelancaran proyek.',
        'Semua aktivitas berjalan baik, tidak ada kendala berarti hari ini.',
        'Sedikit terkendala karena ada permintaan mendadak dari divisi lain.',
        'Target hari ini tercapai meskipun ada beberapa gangguan kecil.',
    ];

    public function run(): void
    {
        mt_srand(2026); // Fixed seed for reproducibility

        $profiles  = $this->buildProfiles();
        $workdays  = $this->getWorkdays();
        $managerIds = $this->loadManagerIds();

        foreach ($profiles as $profile) {
            $this->seedPlans($profile, $workdays, $managerIds);
        }
    }

    private function buildProfiles(): array
    {
        $get = fn($email) => User::where('email', $email)->first();

        return [
            // IT - Kepatuhan tinggi → sedang → rendah
            ['user' => $get('andi@dailyplan.id'),    'division' => 'IT & Pengembangan', 'plan_rate' => 92, 'report_rate' => 88, 'ontime' => 85],
            ['user' => $get('rina@dailyplan.id'),    'division' => 'IT & Pengembangan', 'plan_rate' => 76, 'report_rate' => 72, 'ontime' => 62],
            ['user' => $get('fajar@dailyplan.id'),   'division' => 'IT & Pengembangan', 'plan_rate' => 48, 'report_rate' => 42, 'ontime' => 50],
            // Marketing
            ['user' => $get('deni@dailyplan.id'),    'division' => 'Marketing',          'plan_rate' => 84, 'report_rate' => 79, 'ontime' => 72],
            ['user' => $get('lestari@dailyplan.id'), 'division' => 'Marketing',          'plan_rate' => 66, 'report_rate' => 61, 'ontime' => 55],
            ['user' => $get('rizki@dailyplan.id'),   'division' => 'Marketing',          'plan_rate' => 38, 'report_rate' => 33, 'ontime' => 45],
            // Operasional
            ['user' => $get('mira@dailyplan.id'),    'division' => 'Operasional',        'plan_rate' => 73, 'report_rate' => 68, 'ontime' => 65],
            ['user' => $get('tono@dailyplan.id'),    'division' => 'Operasional',        'plan_rate' => 52, 'report_rate' => 47, 'ontime' => 50],
            ['user' => $get('yoga@dailyplan.id'),    'division' => 'Operasional',        'plan_rate' => 30, 'report_rate' => 25, 'ontime' => 40],
            // HRD
            ['user' => $get('nita@dailyplan.id'),    'division' => 'HRD',                'plan_rate' => 89, 'report_rate' => 84, 'ontime' => 78],
            ['user' => $get('bambang@dailyplan.id'), 'division' => 'HRD',                'plan_rate' => 63, 'report_rate' => 58, 'ontime' => 52],
            ['user' => $get('siti@dailyplan.id'),    'division' => 'HRD',                'plan_rate' => 44, 'report_rate' => 39, 'ontime' => 46],
            // Keuangan
            ['user' => $get('rudi@dailyplan.id'),    'division' => 'Keuangan',           'plan_rate' => 87, 'report_rate' => 82, 'ontime' => 80],
            ['user' => $get('citra@dailyplan.id'),   'division' => 'Keuangan',           'plan_rate' => 71, 'report_rate' => 66, 'ontime' => 68],
            ['user' => $get('wahyu@dailyplan.id'),   'division' => 'Keuangan',           'plan_rate' => 56, 'report_rate' => 50, 'ontime' => 55],
        ];
    }

    private function getWorkdays(): array
    {
        $holidays = [
            '2026-05-01', '2026-05-14',
            '2026-06-01', '2026-06-26',
        ];

        $days = [];
        $current = Carbon::parse('2026-05-04');
        $end     = Carbon::parse('2026-07-02');

        while ($current->lte($end)) {
            $d = $current->format('Y-m-d');
            if (!$current->isWeekend() && !in_array($d, $holidays)) {
                $days[] = $d;
            }
            $current->addDay();
        }

        return $days;
    }

    private function loadManagerIds(): array
    {
        return [
            'IT & Pengembangan' => User::where('email', 'manager.it@dailyplan.id')->value('id'),
            'Marketing'         => User::where('email', 'manager.mkt@dailyplan.id')->value('id'),
            'Operasional'       => User::where('email', 'manager.ops@dailyplan.id')->value('id'),
            'HRD'               => User::where('email', 'manager.hrd@dailyplan.id')->value('id'),
            'Keuangan'          => User::where('email', 'manager.keu@dailyplan.id')->value('id'),
        ];
    }

    private function seedPlans(array $profile, array $workdays, array $managerIds): void
    {
        if (!$profile['user']) return;

        $userId    = $profile['user']->id;
        $division  = $profile['division'];
        $managerId = $managerIds[$division] ?? null;
        $actPool   = $this->pool[$division] ?? $this->pool['IT & Pengembangan'];
        $goalPool  = $this->goalsByDivision[$division] ?? [];
        $today     = '2026-07-02';

        foreach ($workdays as $date) {
            if (mt_rand(1, 100) > $profile['plan_rate']) continue;

            $isPast = $date < $today;
            $onTime = mt_rand(1, 100) <= $profile['ontime'];

            $planHour = $onTime ? mt_rand(7, 8) : mt_rand(9, 10);
            $planAt   = $date . ' ' . sprintf('%02d:%02d:00', $planHour, mt_rand(5, 55));

            $hasReport  = $isPast && (mt_rand(1, 100) <= $profile['report_rate']);
            $reportAt   = null;
            if ($hasReport) {
                $repOnTime = mt_rand(1, 100) <= $profile['ontime'];
                $repHour   = $repOnTime ? mt_rand(16, 19) : mt_rand(20, 22);
                $reportAt  = $date . ' ' . sprintf('%02d:%02d:00', $repHour, mt_rand(5, 55));
            }

            $plan = DailyPlan::create([
                'user_id'             => $userId,
                'plan_date'           => $date,
                'plan_submitted_at'   => $planAt,
                'report_submitted_at' => $reportAt,
                'insight'             => $hasReport ? $this->rand($this->insights) : null,
            ]);

            // Activities
            $count = mt_rand(2, 4);
            $acts  = $this->pickRandom($actPool, $count);
            foreach ($acts as $act) {
                $status    = null;
                $realisasi = null;
                $ket       = null;

                if ($hasReport) {
                    $r      = mt_rand(1, 10);
                    $status = $r <= 7 ? 'selesai' : ($r <= 9 ? 'sebagian' : 'tidak');
                    if ($status === 'selesai') {
                        $realisasi = 'Selesai: ' . $act['desc'];
                    } elseif ($status === 'sebagian') {
                        $realisasi = 'Progress 60% dari: ' . $act['desc'];
                        $ket       = $this->rand(['Terkendala meeting mendadak', 'Menunggu input dari divisi lain', 'Estimasi waktu kurang akurat']);
                    } else {
                        $ket = $this->rand(['Ada prioritas mendesak lain', 'Terkendala akses sistem', 'Perlu koordinasi lebih lanjut']);
                    }
                }

                Activity::create([
                    'daily_plan_id' => $plan->id,
                    'description'   => $act['desc'],
                    'tag'           => $act['tag'],
                    'priority'      => $act['priority'],
                    'status'        => $status,
                    'realisasi'     => $realisasi,
                    'keterangan'    => $ket,
                ]);
            }

            // Goals (70% chance, 1-2 goals)
            if (mt_rand(1, 10) <= 7 && $goalPool) {
                $goals = $this->pickRandom($goalPool, mt_rand(1, 2));
                foreach ($goals as $g) {
                    Goal::create([
                        'daily_plan_id' => $plan->id,
                        'description'   => $g['desc'],
                        'target'        => $g['target'],
                    ]);
                }
            }

            // Feedback (only past plans with report, 45% chance)
            if ($hasReport && $managerId && mt_rand(1, 100) <= 45) {
                $rating   = $this->weightedRating();
                $comment  = $this->rand($this->feedbackComments[$rating]);
                $feedback = Feedback::create([
                    'daily_plan_id' => $plan->id,
                    'manager_id'    => $managerId,
                    'comment'       => $comment,
                    'rating'        => $rating,
                ]);

                // Notify karyawan
                InAppNotification::create([
                    'user_id'    => $userId,
                    'type'       => 'feedback',
                    'title'      => 'Feedback baru dari manager',
                    'body'       => "Rating {$rating}/5 — " . mb_substr($comment, 0, 70) . '...',
                    'url'        => '/daily',
                    'read_at'    => mt_rand(0, 1) ? now()->subHours(mt_rand(1, 48)) : null,
                    'created_at' => $date . ' ' . sprintf('%02d:%02d:00', mt_rand(17, 21), mt_rand(0, 59)),
                    'updated_at' => $date . ' ' . sprintf('%02d:%02d:00', mt_rand(17, 21), mt_rand(0, 59)),
                ]);

                // Reply from karyawan (30% chance)
                if (mt_rand(1, 10) <= 3) {
                    $reply = $this->rand($this->replies);
                    FeedbackReply::create([
                        'feedback_id' => $feedback->id,
                        'user_id'     => $userId,
                        'body'        => $reply,
                    ]);

                    // Notify manager about reply
                    if ($managerId) {
                        InAppNotification::create([
                            'user_id'    => $managerId,
                            'type'       => 'reply',
                            'title'      => 'Karyawan membalas feedback',
                            'body'       => $profile['user']->name . ' membalas feedback laporan tanggal ' . $date,
                            'url'        => '/monitoring/tim',
                            'read_at'    => mt_rand(0, 1) ? now()->subHours(mt_rand(1, 24)) : null,
                            'created_at' => $date . ' ' . sprintf('%02d:%02d:00', mt_rand(18, 22), mt_rand(0, 59)),
                            'updated_at' => $date . ' ' . sprintf('%02d:%02d:00', mt_rand(18, 22), mt_rand(0, 59)),
                        ]);
                    }
                }
            }
        }
    }

    private function weightedRating(): int
    {
        $r = mt_rand(1, 10);
        return match (true) {
            $r <= 2 => 5,
            $r <= 5 => 4,
            $r <= 7 => 3,
            $r <= 9 => 2,
            default => 1,
        };
    }

    private function pickRandom(array $pool, int $n): array
    {
        $idx = range(0, count($pool) - 1);
        for ($i = count($idx) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$idx[$i], $idx[$j]] = [$idx[$j], $idx[$i]];
        }
        return array_map(fn($k) => $pool[$k], array_slice($idx, 0, min($n, count($pool))));
    }

    private function rand(array $arr): mixed
    {
        return $arr[mt_rand(0, count($arr) - 1)];
    }
}
