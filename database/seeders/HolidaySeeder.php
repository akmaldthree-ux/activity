<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['date' => '2026-01-01', 'name' => 'Tahun Baru Masehi'],
            ['date' => '2026-01-27', 'name' => 'Tahun Baru Imlek 2577'],
            ['date' => '2026-01-28', 'name' => 'Isra Mi\'raj Nabi Muhammad SAW'],
            ['date' => '2026-03-28', 'name' => 'Hari Raya Nyepi (Tahun Baru Saka 1948)'],
            ['date' => '2026-04-02', 'name' => 'Wafat Yesus Kristus'],
            ['date' => '2026-04-17', 'name' => 'Cuti Bersama Idul Fitri'],
            ['date' => '2026-04-20', 'name' => 'Hari Raya Idul Fitri 1447 H'],
            ['date' => '2026-04-21', 'name' => 'Hari Raya Idul Fitri 1447 H (Hari Kedua)'],
            ['date' => '2026-04-22', 'name' => 'Cuti Bersama Idul Fitri'],
            ['date' => '2026-04-23', 'name' => 'Cuti Bersama Idul Fitri'],
            ['date' => '2026-04-24', 'name' => 'Cuti Bersama Idul Fitri'],
            ['date' => '2026-05-01', 'name' => 'Hari Buruh Internasional'],
            ['date' => '2026-05-14', 'name' => 'Kenaikan Yesus Kristus'],
            ['date' => '2026-05-23', 'name' => 'Hari Raya Waisak 2570 BE'],
            ['date' => '2026-06-01', 'name' => 'Hari Lahir Pancasila'],
            ['date' => '2026-06-26', 'name' => 'Cuti Bersama Idul Adha'],
            ['date' => '2026-06-27', 'name' => 'Hari Raya Idul Adha 1447 H'],
            ['date' => '2026-07-17', 'name' => 'Tahun Baru Islam 1448 H'],
            ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan Republik Indonesia'],
            ['date' => '2026-09-25', 'name' => 'Maulid Nabi Muhammad SAW'],
            ['date' => '2026-12-25', 'name' => 'Hari Raya Natal'],
        ];

        foreach ($holidays as $h) {
            Holiday::firstOrCreate(['date' => $h['date']], $h);
        }
    }
}
