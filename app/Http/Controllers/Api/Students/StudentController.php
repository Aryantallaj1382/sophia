<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\LearningSubgoal;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $students = Student::where('user_id',$user->id)->first();
       $student = [
         'first_name' => $students->first_name,
         'last_name' => $students->last_name,
         'email' => $students->email,
         'phone' => $students->phone,
         'we_chat' => $students->we_chat,
         'birth_date' => $students->birth_date->format('Y-m-d'),
         'gender' => $students->gender,
         'level' => $students->level,
         'profile' => $students->profile,
           'goal' => $students->learningSubgoals->map(function ($goal) {
               return [
                   'id' => $goal->id,
                    'title' => $goal->title,
                   'title_ch' => $goal->title_ch,
               ];
           }),
       ];
        return api_response($student);

    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'we_chat' => 'nullable',
            'gender' => 'nullable',
            'birth_date' => 'nullable',
            'learning_subgoals' => 'array|nullable', // آی‌دی‌های اهداف
            'learning_subgoals.*' => 'exists:learning_subgoals,id',
            ]);
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        $student->update($request->except('learning_subgoals'));

        if ($request->has('learning_subgoals')) {
            $student->learningSubgoals()->sync($request->learning_subgoals);
        }
        return api_response();
    }
    public function pass(Request $request)
    {

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return api_response([], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return api_response([], 201);
    }
    public function goal(Request $request)
    {
        $lang = $request->header('X-Language', 'en');

        $learningGoals = LearningSubgoal::with(['goal'])
            ->get()
            ->groupBy(function ($learningGoal) use ($lang) {
                $goal = $learningGoal->goal;
                if (!$goal) return null;
                return $lang === 'zh' ? ($goal->title_ch ?? $goal->title) : $goal->title;
            })
            ->map(function ($learningGoalsGroup) use ($lang) {
                $firstGoal = $learningGoalsGroup->first()->goal;

                return [
                    'goal_id' => $firstGoal->id,
                    'goal_title' => $lang === 'zh' ? ($firstGoal->title_ch ?? $firstGoal->title) : $firstGoal->title,
                    'subgoals' => $learningGoalsGroup->map(function ($learningGoal) use ($lang) {
                        return [
                            'value' => $learningGoal->id,
                            'name' => $lang === 'zh' ? ($learningGoal->title_ch ?? $learningGoal->title) : $learningGoal->title,
                        ];
                    })->values(),
                ];
            })
            ->values()
            ->toArray();
        return api_response(['data'=>$learningGoals]);


    }
}
