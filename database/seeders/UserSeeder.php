<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $div = fn($name) => Division::where('name', $name)->value('id');

        $itId  = $div('IT & Pengembangan');
        $mktId = $div('Marketing');
        $opsId = $div('Operasional');
        $hrdId = $div('HRD');
        $keuId = $div('Keuangan');

        // ── Admin ────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'admin@dailyplan.id'], [
            'name'      => 'Admin Sistem',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'jabatan'   => 'System Administrator',
            'no_hp'     => '081200000001',
            'is_active' => true,
            'status'    => 'active',
        ]);

        // ── Managers ─────────────────────────────────────────────
        $managers = [
            ['email' => 'manager.it@dailyplan.id',  'name' => 'Budi Santoso',      'jabatan' => 'Manager IT',         'no_hp' => '081211111101', 'division_id' => $itId],
            ['email' => 'manager.mkt@dailyplan.id', 'name' => 'Sari Dewi Lestari', 'jabatan' => 'Manager Marketing',  'no_hp' => '081211111102', 'division_id' => $mktId],
            ['email' => 'manager.ops@dailyplan.id', 'name' => 'Hendra Kurniawan',  'jabatan' => 'Manager Operasional','no_hp' => '081211111103', 'division_id' => $opsId],
            ['email' => 'manager.hrd@dailyplan.id', 'name' => 'Dewi Rahayu',       'jabatan' => 'Manager HRD',        'no_hp' => '081211111104', 'division_id' => $hrdId],
            ['email' => 'manager.keu@dailyplan.id', 'name' => 'Ahmad Fauzi',       'jabatan' => 'Manager Keuangan',   'no_hp' => '081211111105', 'division_id' => $keuId],
        ];

        foreach ($managers as $m) {
            User::firstOrCreate(['email' => $m['email']], array_merge($m, [
                'password'  => Hash::make('password'),
                'role'      => 'manager',
                'is_active' => true,
                'status'    => 'active',
            ]));
        }

        // ── Karyawan IT ──────────────────────────────────────────
        $karyawan = [
            // IT
            ['email' => 'andi@dailyplan.id',    'name' => 'Andi Pratama',      'jabatan' => 'Frontend Developer',    'no_hp' => '08122000101', 'division_id' => $itId],
            ['email' => 'rina@dailyplan.id',    'name' => 'Rina Wulandari',    'jabatan' => 'Backend Developer',     'no_hp' => '08122000102', 'division_id' => $itId],
            ['email' => 'fajar@dailyplan.id',   'name' => 'Fajar Nugroho',     'jabatan' => 'DevOps Engineer',       'no_hp' => '08122000103', 'division_id' => $itId],
            // Marketing
            ['email' => 'deni@dailyplan.id',    'name' => 'Deni Saputra',      'jabatan' => 'Marketing Specialist',  'no_hp' => '08122000201', 'division_id' => $mktId],
            ['email' => 'lestari@dailyplan.id', 'name' => 'Lestari Putri',     'jabatan' => 'Content Creator',       'no_hp' => '08122000202', 'division_id' => $mktId],
            ['email' => 'rizki@dailyplan.id',   'name' => 'Rizki Firmansyah',  'jabatan' => 'Digital Marketing',     'no_hp' => '08122000203', 'division_id' => $mktId],
            // Operasional
            ['email' => 'tono@dailyplan.id',    'name' => 'Tono Subianto',     'jabatan' => 'Staff Operasional',     'no_hp' => '08122000301', 'division_id' => $opsId],
            ['email' => 'mira@dailyplan.id',    'name' => 'Mira Agustina',     'jabatan' => 'Koordinator Logistik',  'no_hp' => '08122000302', 'division_id' => $opsId],
            ['email' => 'yoga@dailyplan.id',    'name' => 'Yoga Ramadhan',     'jabatan' => 'Staff Gudang',          'no_hp' => '08122000303', 'division_id' => $opsId],
            // HRD
            ['email' => 'nita@dailyplan.id',    'name' => 'Nita Sriwijaya',    'jabatan' => 'HR Specialist',         'no_hp' => '08122000401', 'division_id' => $hrdId],
            ['email' => 'bambang@dailyplan.id', 'name' => 'Bambang Purnomo',   'jabatan' => 'Recruitment Staff',     'no_hp' => '08122000402', 'division_id' => $hrdId],
            ['email' => 'siti@dailyplan.id',    'name' => 'Siti Aminah',       'jabatan' => 'Training Officer',      'no_hp' => '08122000403', 'division_id' => $hrdId],
            // Keuangan
            ['email' => 'rudi@dailyplan.id',    'name' => 'Rudi Hartono',      'jabatan' => 'Accounting Staff',      'no_hp' => '08122000501', 'division_id' => $keuId],
            ['email' => 'citra@dailyplan.id',   'name' => 'Citra Lestari',     'jabatan' => 'Finance Analyst',       'no_hp' => '08122000502', 'division_id' => $keuId],
            ['email' => 'wahyu@dailyplan.id',   'name' => 'Wahyu Hidayat',     'jabatan' => 'Tax Officer',           'no_hp' => '08122000503', 'division_id' => $keuId],
        ];

        foreach ($karyawan as $k) {
            User::firstOrCreate(['email' => $k['email']], array_merge($k, [
                'password'  => Hash::make('password'),
                'role'      => 'karyawan',
                'is_active' => true,
                'status'    => 'active',
            ]));
        }

        // ── Demo approval flow ────────────────────────────────────
        User::firstOrCreate(['email' => 'calon1@dailyplan.id'], [
            'name'        => 'Calon Karyawan Baru',
            'password'    => Hash::make('password'),
            'role'        => 'karyawan',
            'jabatan'     => 'Staff IT',
            'no_hp'       => '08123456789',
            'division_id' => $itId,
            'is_active'   => false,
            'status'      => 'pending',
        ]);

        User::firstOrCreate(['email' => 'calon2@dailyplan.id'], [
            'name'        => 'Pelamar Sudah Manager',
            'password'    => Hash::make('password'),
            'role'        => 'karyawan',
            'jabatan'     => 'Staff Marketing',
            'no_hp'       => '08129876543',
            'division_id' => $mktId,
            'is_active'   => false,
            'status'      => 'manager_approved',
        ]);
    }
}
