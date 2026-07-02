<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year      = $request->integer('year', now()->year);
        $holidays  = Holiday::whereYear('date', $year)->orderBy('date')->get();
        $years     = range(now()->year - 1, now()->year + 2);

        return view('admin.holidays.index', compact('holidays', 'year', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date', 'unique:holidays,date'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Holiday::create($request->only('date', 'name'));

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return back()->with('success', 'Hari libur berhasil dihapus.');
    }

    public function sync(Request $request)
    {
        $year = $request->integer('year', now()->year);
        $url  = "https://date.nager.at/api/v3/PublicHolidays/{$year}/ID";

        try {
            $response = Http::timeout(15)->get($url);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke API: ' . $e->getMessage());
        }

        if (!$response->successful()) {
            return back()->with('error', "API error (HTTP {$response->status()}). Coba lagi nanti.");
        }

        $data  = $response->json();
        $added = 0;

        foreach ($data as $item) {
            $date = $item['date'] ?? null;
            $name = $item['localName'] ?? $item['name'] ?? null;
            if (!$date || !$name) continue;

            $exists = Holiday::where('date', $date)->exists();
            Holiday::updateOrCreate(['date' => $date], ['name' => $name]);
            if (!$exists) $added++;
        }

        $msg = "Sinkronisasi tahun {$year} selesai. {$added} hari libur baru ditambahkan dari " . count($data) . " total data.";
        return back()->with('success', $msg);
    }
}
