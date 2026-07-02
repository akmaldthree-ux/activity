<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncHolidays extends Command
{
    protected $signature   = 'holidays:sync {year? : Tahun yang disinkronkan (default: tahun ini)}';
    protected $description = 'Sinkronisasi hari libur nasional Indonesia dari API kalender publik';

    public function handle(): int
    {
        $year = (int) ($this->argument('year') ?? now()->year);
        $url  = "https://date.nager.at/api/v3/PublicHolidays/{$year}/ID";

        $this->info("Mengambil data hari libur {$year} dari {$url}...");

        try {
            $response = Http::timeout(15)->get($url);
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke API: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (!$response->successful()) {
            $this->error("API mengembalikan status {$response->status()}.");
            return self::FAILURE;
        }

        $data = $response->json();
        if (empty($data)) {
            $this->warn("Tidak ada data hari libur untuk tahun {$year}.");
            return self::SUCCESS;
        }

        $added   = 0;
        $skipped = 0;

        foreach ($data as $item) {
            $date = $item['date'] ?? null;
            $name = $item['localName'] ?? $item['name'] ?? null;

            if (!$date || !$name) continue;

            $existed = Holiday::where('date', $date)->exists();

            Holiday::updateOrCreate(
                ['date' => $date],
                ['name' => $name]
            );

            if ($existed) {
                $skipped++;
            } else {
                $added++;
                $this->line("  + {$date} — {$name}");
            }
        }

        $this->info("Selesai. Ditambahkan: {$added}, sudah ada: {$skipped}.");
        return self::SUCCESS;
    }
}
