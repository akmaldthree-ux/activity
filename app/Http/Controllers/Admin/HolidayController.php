<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

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
}
