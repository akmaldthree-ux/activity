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
        $itDiv   = Division::where('name', 'IT & Pengembangan')->first();
        $mktDiv  = Division::where('name', 'Marketing')->first();
        $opsDiv  = Division::where('name', 'Operasional')->first();

        // Admin (tanpa divisi)
        User::firstOrCreate(
            ['email' => 'admin@dailyplan.id'],
            [
                'name'     => 'Admin Sistem',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'division_id' => null,
                'is_active' => true,
                'status'    => 'active',
            ]
        );

        // Manager per divisi
        User::firstOrCreate(
            ['email' => 'manager.it@dailyplan.id'],
            [
                'name'        => 'Budi Manager IT',
                'password'    => Hash::make('password'),
                'role'        => 'manager',
                'division_id' => $itDiv?->id,
                'is_active'   => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager.mkt@dailyplan.id'],
            [
                'name'        => 'Sari Manager Marketing',
                'password'    => Hash::make('password'),
                'role'        => 'manager',
                'division_id' => $mktDiv?->id,
                'is_active'   => true,
            ]
        );

        // Karyawan IT
        foreach ([
            ['name' => 'Andi Developer', 'email' => 'andi@dailyplan.id'],
            ['name' => 'Rina Backend',   'email' => 'rina@dailyplan.id'],
        ] as $k) {
            User::firstOrCreate(
                ['email' => $k['email']],
                [
                    'name'        => $k['name'],
                    'password'    => Hash::make('password'),
                    'role'        => 'karyawan',
                    'division_id' => $itDiv?->id,
                    'is_active'   => true,
                ]
            );
        }

        // Karyawan Marketing
        User::firstOrCreate(
            ['email' => 'deni@dailyplan.id'],
            [
                'name'        => 'Deni Marketing',
                'password'    => Hash::make('password'),
                'role'        => 'karyawan',
                'division_id' => $mktDiv?->id,
                'is_active'   => true,
            ]
        );

        // Karyawan Operasional
        User::firstOrCreate(
            ['email' => 'tono@dailyplan.id'],
            [
                'name'        => 'Tono Operasional',
                'password'    => Hash::make('password'),
                'role'        => 'karyawan',
                'division_id' => $opsDiv?->id,
                'is_active'   => true,
            ]
        );
    }
}
