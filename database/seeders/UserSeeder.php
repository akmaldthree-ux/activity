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
        $admin = User::create([
            'name'      => 'Admin Sistem',
            'email'     => 'admin@dailyplan.id',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'jabatan'   => 'System Administrator',
            'no_hp'     => '081200000001',
            'is_active' => true,
            'status'    => 'active',
        ]);

        // ── Direksi ──────────────────────────────────────────────
        $direksi = User::create([
            'name'      => 'Drs. Ahmad Suryadi',
            'email'     => 'direksi@dailyplan.id',
            'password'  => Hash::make('password'),
            'role'      => 'direksi',
            'jabatan'   => 'Direktur Utama',
            'no_hp'     => '081200000010',
            'is_active' => true,
            'status'    => 'active',
        ]);

        // ── Managers (reports_to: direksi) ───────────────────────
        $managerData = [
            ['email' => 'manager.it@dailyplan.id',  'name' => 'Budi Santoso',      'jabatan' => 'Manager IT',         'no_hp' => '081211111101', 'division_id' => $itId],
            ['email' => 'manager.mkt@dailyplan.id', 'name' => 'Sari Dewi Lestari', 'jabatan' => 'Manager Marketing',  'no_hp' => '081211111102', 'division_id' => $mktId],
            ['email' => 'manager.ops@dailyplan.id', 'name' => 'Hendra Kurniawan',  'jabatan' => 'Manager Operasional','no_hp' => '081211111103', 'division_id' => $opsId],
            ['email' => 'manager.hrd@dailyplan.id', 'name' => 'Dewi Rahayu',       'jabatan' => 'Manager HRD',        'no_hp' => '081211111104', 'division_id' => $hrdId],
            ['email' => 'manager.keu@dailyplan.id', 'name' => 'Ahmad Fauzi',       'jabatan' => 'Manager Keuangan',   'no_hp' => '081211111105', 'division_id' => $keuId],
        ];

        $managers = [];
        foreach ($managerData as $m) {
            $managers[$m['email']] = User::create(array_merge($m, [
                'password'   => Hash::make('password'),
                'role'       => 'manager',
                'reports_to' => $direksi->id,
                'is_active'  => true,
                'status'     => 'active',
            ]));
        }

        // ── Leaders (one per division, reports_to: their division's manager) ──
        $leaderData = [
            ['email' => 'leader.it@dailyplan.id',  'name' => 'Reza Firmansyah',  'jabatan' => 'Team Lead IT',         'no_hp' => '081211112101', 'division_id' => $itId,  'mgr' => 'manager.it@dailyplan.id'],
            ['email' => 'leader.mkt@dailyplan.id', 'name' => 'Fitri Handayani',  'jabatan' => 'Team Lead Marketing',  'no_hp' => '081211112102', 'division_id' => $mktId, 'mgr' => 'manager.mkt@dailyplan.id'],
            ['email' => 'leader.ops@dailyplan.id', 'name' => 'Agus Setiawan',    'jabatan' => 'Team Lead Operasional','no_hp' => '081211112103', 'division_id' => $opsId, 'mgr' => 'manager.ops@dailyplan.id'],
            ['email' => 'leader.hrd@dailyplan.id', 'name' => 'Maya Kusuma',      'jabatan' => 'Team Lead HRD',        'no_hp' => '081211112104', 'division_id' => $hrdId, 'mgr' => 'manager.hrd@dailyplan.id'],
            ['email' => 'leader.keu@dailyplan.id', 'name' => 'Yusuf Pratama',    'jabatan' => 'Team Lead Keuangan',   'no_hp' => '081211112105', 'division_id' => $keuId, 'mgr' => 'manager.keu@dailyplan.id'],
        ];

        $leaders = [];
        foreach ($leaderData as $l) {
            $leaders[$l['email']] = User::create([
                'name'        => $l['name'],
                'email'       => $l['email'],
                'password'    => Hash::make('password'),
                'role'        => 'leader',
                'jabatan'     => $l['jabatan'],
                'no_hp'       => $l['no_hp'],
                'division_id' => $l['division_id'],
                'reports_to'  => $managers[$l['mgr']]->id,
                'is_active'   => true,
                'status'      => 'active',
            ]);
        }

        // ── Karyawan (reports_to: their division's leader) ───────
        $karyawanData = [
            // IT
            ['email' => 'andi@dailyplan.id',    'name' => 'Andi Pratama',      'jabatan' => 'Frontend Developer',    'no_hp' => '08122000101', 'division_id' => $itId,  'leader' => 'leader.it@dailyplan.id'],
            ['email' => 'rina@dailyplan.id',    'name' => 'Rina Wulandari',    'jabatan' => 'Backend Developer',     'no_hp' => '08122000102', 'division_id' => $itId,  'leader' => 'leader.it@dailyplan.id'],
            ['email' => 'fajar@dailyplan.id',   'name' => 'Fajar Nugroho',     'jabatan' => 'DevOps Engineer',       'no_hp' => '08122000103', 'division_id' => $itId,  'leader' => 'leader.it@dailyplan.id'],
            // Marketing
            ['email' => 'deni@dailyplan.id',    'name' => 'Deni Saputra',      'jabatan' => 'Marketing Specialist',  'no_hp' => '08122000201', 'division_id' => $mktId, 'leader' => 'leader.mkt@dailyplan.id'],
            ['email' => 'lestari@dailyplan.id', 'name' => 'Lestari Putri',     'jabatan' => 'Content Creator',       'no_hp' => '08122000202', 'division_id' => $mktId, 'leader' => 'leader.mkt@dailyplan.id'],
            ['email' => 'rizki@dailyplan.id',   'name' => 'Rizki Firmansyah',  'jabatan' => 'Digital Marketing',     'no_hp' => '08122000203', 'division_id' => $mktId, 'leader' => 'leader.mkt@dailyplan.id'],
            // Operasional
            ['email' => 'tono@dailyplan.id',    'name' => 'Tono Subianto',     'jabatan' => 'Staff Operasional',     'no_hp' => '08122000301', 'division_id' => $opsId, 'leader' => 'leader.ops@dailyplan.id'],
            ['email' => 'mira@dailyplan.id',    'name' => 'Mira Agustina',     'jabatan' => 'Koordinator Logistik',  'no_hp' => '08122000302', 'division_id' => $opsId, 'leader' => 'leader.ops@dailyplan.id'],
            ['email' => 'yoga@dailyplan.id',    'name' => 'Yoga Ramadhan',     'jabatan' => 'Staff Gudang',          'no_hp' => '08122000303', 'division_id' => $opsId, 'leader' => 'leader.ops@dailyplan.id'],
            // HRD
            ['email' => 'nita@dailyplan.id',    'name' => 'Nita Sriwijaya',    'jabatan' => 'HR Specialist',         'no_hp' => '08122000401', 'division_id' => $hrdId, 'leader' => 'leader.hrd@dailyplan.id'],
            ['email' => 'bambang@dailyplan.id', 'name' => 'Bambang Purnomo',   'jabatan' => 'Recruitment Staff',     'no_hp' => '08122000402', 'division_id' => $hrdId, 'leader' => 'leader.hrd@dailyplan.id'],
            ['email' => 'siti@dailyplan.id',    'name' => 'Siti Aminah',       'jabatan' => 'Training Officer',      'no_hp' => '08122000403', 'division_id' => $hrdId, 'leader' => 'leader.hrd@dailyplan.id'],
            // Keuangan
            ['email' => 'rudi@dailyplan.id',    'name' => 'Rudi Hartono',      'jabatan' => 'Accounting Staff',      'no_hp' => '08122000501', 'division_id' => $keuId, 'leader' => 'leader.keu@dailyplan.id'],
            ['email' => 'citra@dailyplan.id',   'name' => 'Citra Lestari',     'jabatan' => 'Finance Analyst',       'no_hp' => '08122000502', 'division_id' => $keuId, 'leader' => 'leader.keu@dailyplan.id'],
            ['email' => 'wahyu@dailyplan.id',   'name' => 'Wahyu Hidayat',     'jabatan' => 'Tax Officer',           'no_hp' => '08122000503', 'division_id' => $keuId, 'leader' => 'leader.keu@dailyplan.id'],
        ];

        foreach ($karyawanData as $k) {
            User::create([
                'name'        => $k['name'],
                'email'       => $k['email'],
                'password'    => Hash::make('password'),
                'role'        => 'karyawan',
                'jabatan'     => $k['jabatan'],
                'no_hp'       => $k['no_hp'],
                'division_id' => $k['division_id'],
                'reports_to'  => $leaders[$k['leader']]->id,
                'is_active'   => true,
                'status'      => 'active',
            ]);
        }

        // ── Demo approval flow ────────────────────────────────────
        User::create([
            'name'        => 'Calon Karyawan Baru',
            'email'       => 'calon1@dailyplan.id',
            'password'    => Hash::make('password'),
            'role'        => 'karyawan',
            'jabatan'     => 'Staff IT',
            'no_hp'       => '08123456789',
            'division_id' => $itId,
            'is_active'   => false,
            'status'      => 'pending',
        ]);

        User::create([
            'name'        => 'Pelamar Sudah Manager',
            'email'       => 'calon2@dailyplan.id',
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
