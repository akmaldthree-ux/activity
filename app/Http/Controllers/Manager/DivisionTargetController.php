<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionTargetController extends Controller
{
    public function index(Request $request)
    {
        $manager    = Auth::user();
        $divisionId = $manager->isAdmin() ? null : $manager->division_id;

        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $divisionsQuery = Division::query()->when($divisionId, fn($q) => $q->where('id', $divisionId));
        $divisions = $divisionsQuery->orderBy('name')->get();

        $targets = DivisionTarget::where('year', $year)->where('month', $month)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->get()
            ->keyBy('division_id');

        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create($year, $m)->translatedFormat('F')]);

        return view('manager.targets', compact('divisions', 'targets', 'year', 'month', 'months'));
    }

    public function store(Request $request)
    {
        $manager = Auth::user();

        $request->validate([
            'division_id'      => ['required', 'exists:divisions,id'],
            'year'             => ['required', 'integer', 'min:2020', 'max:2100'],
            'month'            => ['required', 'integer', 'min:1', 'max:12'],
            'plan_target_pct'  => ['required', 'integer', 'min:0', 'max:100'],
            'report_target_pct'=> ['required', 'integer', 'min:0', 'max:100'],
            'note'             => ['nullable', 'string', 'max:500'],
        ]);

        // Manager hanya bisa set target divisinya sendiri
        if ($manager->isManager() && $request->division_id != $manager->division_id) {
            abort(403);
        }

        DivisionTarget::updateOrCreate(
            ['division_id' => $request->division_id, 'year' => $request->year, 'month' => $request->month],
            [
                'set_by'            => $manager->id,
                'plan_target_pct'   => $request->plan_target_pct,
                'report_target_pct' => $request->report_target_pct,
                'note'              => $request->note,
            ]
        );

        return back()->with('success', 'Target berhasil disimpan.');
    }
}
