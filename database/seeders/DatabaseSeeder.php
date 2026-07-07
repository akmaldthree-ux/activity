<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('in_app_notifications')->truncate();
        DB::table('division_targets')->truncate();
        DB::table('activity_templates')->truncate();
        DB::table('announcements')->truncate();
        DB::table('feedback_replies')->truncate();
        DB::table('feedbacks')->truncate();
        DB::table('report_attachments')->truncate();
        DB::table('activities')->truncate();
        DB::table('goals')->truncate();
        DB::table('daily_plans')->truncate();
        DB::table('holidays')->truncate();
        DB::table('users')->truncate();
        DB::table('divisions')->truncate();
        Schema::enableForeignKeyConstraints();

        // All seeded from scratch each run
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
