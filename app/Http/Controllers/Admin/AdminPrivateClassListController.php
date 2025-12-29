<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Models\LanguageLevel;
use App\Models\LearningSubgoal;
use App\Models\Platform;
use App\Models\PrivateClassReservation;
use App\Models\Professor;
use App\Models\Skill;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminPrivateClassListController extends Controller
{
    public function update(Request $request, $id)
    {
        $reservation = PrivateClassReservation::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'sessions_count' => 'required|integer|min:1',
            'class_type' => 'required|string',
            'platform_id' => 'nullable|integer',
            'language_level_id' => 'nullable|integer',
            'age_group_id' => 'nullable|integer',
            'skill_id' => 'nullable|integer',
            'professor_id' => 'nullable|integer',
            'subgoal_id' => 'nullable|integer',
            'student_id' => 'nullable|integer',
        ]);

        $reservation->update($request->all());

        return back()->with('success', 'تغییرات با موفقیت ذخیره شد.');
    }

    public function index(Request $request)
    {
        $query = PrivateClassReservation::with(['user', 'professor', 'timeSlots'])->latest();

        // اگر فیلتر ارسال شده بود
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $reservations = $query->get();

        return view('admin.private_classes.index', compact('reservations'));
    }

    public function show($id)
    {
        $reservations = PrivateClassReservation::with([
            'user',
            'professor',
            'ageGroup',
            'languageLevel',
            'platform',
            'subgoal',
            'skill',
            'timeSlots' => function($query) {
                $query->orderBy('date', 'asc')
                    ->orderBy('time', 'asc');
            }
        ])->findOrFail($id);

        // داده‌های کمکی برای فرم ویرایش
        $platforms   = Platform::all();        // پلتفرم‌ها
        $professors   = Professor::all();        // پلتفرم‌ها
        $students   = Student::all();        // پلتفرم‌ها
        $levels      = LanguageLevel::all();   // سطح زبان
        $ageGroups   = AgeGroup::all();        // گروه سنی
        $skills      = Skill::all();           // مهارت‌ها
        $subgoals    = LearningSubgoal::all();         // هدف فرعی

        return view('admin.private_classes.show', compact(
            'reservations',
            'platforms',
            'levels',
            'students',
            'ageGroups',
            'professors',
            'skills',
            'subgoals'
        ));
    }

    public function updateClassLink(Request $request, $id)
    {
        $request->validate([
            'class_link' => 'nullable|url|max:255',
        ]);

        $reservation = PrivateClassReservation::findOrFail($id);
        $reservation->class_link = $request->class_link;
        $reservation->save();

        return redirect()->back()->with('success', 'لینک کلاس با موفقیت بروزرسانی شد.');
    }

}
