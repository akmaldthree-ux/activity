<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            DivisionSeeder::class,
            HolidaySeeder::class,
            UserSeeder::class,
            DailyPlanSeeder::class,
            ActivityTemplateSeeder::class,
            AnnouncementSeeder::class,
            DivisionTargetSeeder::class,
        ]);
    }
}
