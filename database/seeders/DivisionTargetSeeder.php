<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\DivisionTarget;
use App\Models\User;
use Illuminate\Database\Seeder;

class DivisionTargetSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = Division::all()->keyBy('name');

        $managerEmail = [
            'IT & Pengembangan' => 'manager.it@dailyplan.id',
            'Marketing'         => 'manager.mkt@dailyplan.id',
            'Operasional'       => 'manager.ops@dailyplan.id',
            'HRD'               => 'manager.hrd@dailyplan.id',
            'Keuangan'          => 'manager.keu@dailyplan.id',
        ];

        // Target per division per month (May, June, July 2026)
        $targets = [
            'IT & Pengembangan' => [
                [2026, 5, 85, 80, 'Target agresif Q2. Fokus konsistensi pengisian plan & report.'],
                [2026, 6, 85, 80, 'Pertahankan performa bagus bulan Mei.'],
                [2026, 7, 90, 85, 'Tingkatkan target menjelang review H1.'],
            ],
            'Marketing' => [
                [2026, 5, 80, 75, 'Target Q2 Marketing. Prioritas konsistensi laporan harian.'],
                [2026, 6, 80, 75, 'Konsisten dengan target Mei.'],
                [2026, 7, 85, 80, 'Tingkatkan untuk persiapan Q3.'],
            ],
            'Operasional' => [
                [2026, 5, 75, 70, 'Realistis dengan beban kerja operasional. Fokus perbaikan kepatuhan.'],
                [2026, 6, 75, 70, 'Pertahankan pencapaian Mei.'],
                [2026, 7, 80, 75, 'Target lebih tinggi setelah perbaikan SOP.'],
            ],
            'HRD' => [
                [2026, 5, 85, 80, 'HRD diharapkan jadi contoh kepatuhan untuk divisi lain.'],
                [2026, 6, 85, 80, 'Pertahankan standar tinggi.'],
                [2026, 7, 90, 85, 'Target tertinggi sesuai peran strategis HRD.'],
            ],
            'Keuangan' => [
                [2026, 5, 80, 75, 'Kepatuhan tinggi penting untuk akurasi data keuangan.'],
                [2026, 6, 80, 78, 'Sedikit tingkatkan target report.'],
                [2026, 7, 85, 80, 'Persiapan audit H1 memerlukan kelengkapan data.'],
            ],
        ];

        foreach ($targets as $divName => $months) {
            $div = $divisions[$divName] ?? null;
            if (!$div) continue;

            $managerId = User::where('email', $managerEmail[$divName])->value('id');

            foreach ($months as [$year, $month, $planPct, $repPct, $note]) {
                DivisionTarget::updateOrCreate(
                    ['division_id' => $div->id, 'year' => $year, 'month' => $month],
                    [
                        'set_by'            => $managerId,
                        'plan_target_pct'   => $planPct,
                        'report_target_pct' => $repPct,
                        'note'              => $note,
                    ]
                );
            }
        }
    }
}
