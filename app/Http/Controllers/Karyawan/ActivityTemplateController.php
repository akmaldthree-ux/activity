<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\ActivityTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityTemplateController extends Controller
{
    public function index()
    {
        $templates = ActivityTemplate::where('user_id', Auth::id())->orderBy('description')->get();
        return response()->json($templates);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => ['required', 'string', 'max:500'],
            'priority'    => ['required', 'in:tinggi,sedang,rendah'],
        ]);

        $template = ActivityTemplate::firstOrCreate(
            ['user_id' => Auth::id(), 'description' => $request->description],
            ['priority' => $request->priority]
        );

        return response()->json($template, 201);
    }

    public function destroy(ActivityTemplate $template)
    {
        abort_if($template->user_id !== Auth::id(), 403);
        $template->delete();
        return response()->json(['ok' => true]);
    }
}
