<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeDevData extends Command
{
    protected $signature   = 'app:purge-dev-data {--force : Skip confirmation prompt}';
    protected $description = 'Hapus semua data dummy/dev kecuali admin dan hari libur';

    public function handle(): int
    {
        if (!$this->option('force')) {
            $this->warn('Perintah ini akan menghapus SEMUA data kecuali akun admin dan hari libur.');
            if (!$this->confirm('Lanjutkan?')) {
                $this->info('Dibatalkan.');
                return self::SUCCESS;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('in_app_notifications')->truncate();
        DB::table('division_targets')->truncate();
        DB::table('activity_templates')->truncate();
        DB::table('announcements')->truncate();
        DB::table('feedback_replies')->truncate();
        DB::table('feedbacks')->truncate();
        DB::table('activities')->truncate();
        DB::table('goals')->truncate();
        DB::table('report_attachments')->truncate();
        DB::table('daily_plans')->truncate();

        // Hapus semua user kecuali admin
        DB::table('users')->where('role', '!=', 'admin')->delete();

        // Hapus semua divisi
        DB::table('divisions')->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Selesai. Data dummy telah dihapus.');
        $this->info('Tersisa: ' . DB::table('users')->count() . ' user (admin), ' . DB::table('holidays')->count() . ' hari libur.');

        return self::SUCCESS;
    }
}
