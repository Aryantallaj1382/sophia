<?php

namespace App\Http\Controllers\Api\Exam;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Models\Exam;
use App\Models\LanguageLevel;
use App\Models\Skill;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function info()
    {
        $skills = Skill::all();
        $level = LanguageLevel::all();
        return api_response([
            'skills' => $skills,
            'level' => $level,
        ]);

    }

    public function placement(Request $request)
    {
        $user = auth()->user()->student;
        $age = $user->age_group;

        $level = $request->level_id;
        $skills = $request->skill_id;
        $exam = Exam::where('language_level_id', $level)->where('skill_id', $skills)->where('age_group_id' , $age)->first();

        if (!$exam) {
            return api_response([],'exam not found', 422);
        }
        return api_response(['id'=>$exam->id]);
    }
}
