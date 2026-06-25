<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'IT & Pengembangan', 'description' => 'Divisi teknologi informasi dan pengembangan sistem'],
            ['name' => 'Marketing', 'description' => 'Divisi pemasaran dan promosi'],
            ['name' => 'Operasional', 'description' => 'Divisi operasional dan logistik'],
            ['name' => 'HRD', 'description' => 'Divisi sumber daya manusia'],
            ['name' => 'Keuangan', 'description' => 'Divisi keuangan dan akuntansi'],
        ];

        foreach ($divisions as $div) {
            Division::firstOrCreate(['name' => $div['name']], $div);
        }
    }
}
