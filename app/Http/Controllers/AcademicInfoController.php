<?php

namespace App\Http\Controllers;

use App\Models\AcademicInfo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicInfoController extends Controller
{
    public function cutOffMarks(Request $request): View
    {
        $query = AcademicInfo::cutOffMarks();

        if ($request->filled('city')) {
            $query->byCity($request->input('city'));
        }
        if ($request->filled('min_mark')) {
            $query->where('cut_off_mark', '>=', $request->input('min_mark'));
        }
        if ($request->filled('max_mark')) {
            $query->where('cut_off_mark', '<=', $request->input('max_mark'));
        }

        $marks = $query->orderBy('cut_off_mark', 'asc')->paginate(20);
        return view('academic.cut-off-marks', compact('marks'));
    }

    public function calculator(Request $request): View
    {
        $grade = $request->input('grade');
        $city = $request->input('city');
        $query = AcademicInfo::cutOffMarks();
        if ($grade !== null) {
            $query->withCutOffBelow($grade);
        }
        if ($city) {
            $query->byCity($city);
        }
        $results = $query->orderBy('cut_off_mark')->get();
        return view('academic.calculator', compact('results', 'grade', 'city'));
    }
}
