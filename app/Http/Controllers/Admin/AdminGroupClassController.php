<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Models\Book;
use App\Models\GroupClass;
use App\Models\GroupClassReservation;
use App\Models\GroupClassSchedule;
use App\Models\Language;
use App\Models\LanguageLevel;
use App\Models\LearningSubgoal;
use App\Models\Platform;
use App\Models\Professor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AdminGroupClassController extends Controller
{
    public function index()
    {
        $classes = GroupClass::with('subject')->latest()->paginate(12);
        return view('admin.group_classes.index', compact('classes'));
    }
    public function show($id)
    {
        $class = GroupClass::with(['professor.user', 'language', 'level', 'ageGroup', 'platform', 'schedules'])
            ->findOrFail($id);

        return view('admin.group_classes.show', compact('class'));
    }
    public function create()
    {
        $professors = Professor::with('user')->get();
        $languages = Language::all();
        $levels = LanguageLevel::all();
        $subgoals = LearningSubgoal::all();
        $ageGroups = AgeGroup::all();
        $platforms = Platform::all();
        $books = Book::all();

        return view('admin.group-classes.create', compact(
            'professors',
            'languages',
            'levels',
            'subgoals',
            'ageGroups',
            'platforms',
            'books'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'professor_id' => 'required|exists:professors,id',
            'language_id' => 'required|exists:languages,id',
            'language_level_id' => 'required|exists:language_levels,id',
            'subject_id' => 'required|exists:learning_subgoals,id',
            'age_group_id' => 'required|exists:age_groups,id',
            'platform_id' => 'required|exists:platforms,id',
            'book_id' => 'nullable|exists:books,id',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|gte:min_capacity',
            'sessions_count' => 'required|integer|min:1',
            'hourly' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_link' => 'nullable|url',
            'total_price' => 'nullable|numeric|min:0',
            'new_total_price' => 'nullable|numeric|min:0',
            'total_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:2048',
            'days' => 'required|array',
            'days.*' => 'string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // 📌 آپلود عکس
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('group_classes'), $filename);
            $validated['image'] = 'group_classes/' . $filename;
        }

        $groupClassData = collect($validated)->except(['days', 'start_time', 'end_time'])->toArray();
        $groupClass = GroupClass::create($groupClassData);
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dayName = $date->format('l'); // Saturday, Sunday, ...

            if (in_array($dayName, $request->days)) {
                $groupClass->schedules()->create([
                    'day'        => $dayName,
                    'date'       => $date, // میلادی رسمی
                    'start_time' => $request->start_time,
                    'end_time'   => $request->end_time,
                ]);
            }
        }

        return redirect()->route('admin.group_class.index')
            ->with('success', 'کلاس گروهی با موفقیت ایجاد شد.');
    }

    public function edit(GroupClass $groupClass)
    {
        // دریافت داده‌های لازم برای select ها
        $professors  = Professor::with('user')->get();
        $languages   = Language::all();
        $levels      = LanguageLevel::all();
        $subgoals    = LearningSubgoal::all();
        $ageGroups   = AgeGroup::all();
        $platforms   = Platform::all();
        $books       = Book::all();

        // نمایش صفحه ویرایش
        return view('admin.group_class.edit', [
            'groupClass' => $groupClass,
            'professors' => $professors,
            'languages'  => $languages,
            'levels'     => $levels,
            'subgoals'   => $subgoals,
            'ageGroups'  => $ageGroups,
            'platforms'  => $platforms,
            'books'      => $books,
        ]);
    }

    public function update(Request $request, GroupClass $groupClass)
    {
        $validated = $request->validate([
            'professor_id' => 'required|exists:professors,id',
            'language_id' => 'required|exists:languages,id',
            'language_level_id' => 'required|exists:language_levels,id',
            'subject_id' => 'required|exists:learning_subgoals,id',
            'age_group_id' => 'required|exists:age_groups,id',
            'platform_id' => 'required|exists:platforms,id',
            'book_id' => 'nullable|exists:books,id',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|gte:min_capacity',
            'sessions_count' => 'required|integer|min:1',
            'hourly' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_link' => 'nullable|url',
            'total_price' => 'nullable|numeric|min:0',
            'new_total_price' => 'nullable|numeric|min:0',
            'total_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:2048',
            'days' => 'nullable|array',
            'days.*' => 'string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        // 📌 آپلود عکس جدید (اگر وجود داشت)
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('group_classes'), $filename);
            $validated['image'] = 'group_classes/' . $filename;
        }

        // فقط فیلدهای اصلی کلاس را آپدیت کن
        $groupClassData = collect($validated)->except(['days', 'start_time', 'end_time'])->toArray();
        $groupClass->update($groupClassData);

        // 📌 ریست کردن برنامه جلسات و ثبت دوباره
        $groupClass->schedules()->delete();

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dayName = $date->format('l'); // Saturday, Sunday, ...

            if (in_array($dayName, $request->days)) {
                $groupClass->schedules()->create([
                    'day'        => $dayName,
                    'date'       => $date,
                    'start_time' => $request->start_time,
                    'end_time'   => $request->end_time,
                ]);
            }
        }

        return redirect()->route('admin.group_class.index')
            ->with('success', 'کلاس گروهی با موفقیت ویرایش شد.');
    }
    public function updateSchedule(Request $request, GroupClassSchedule $schedule)
    {
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // آپدیت رکورد
        $schedule->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->back()->with('success', 'زمان‌بندی با موفقیت بروزرسانی شد.');
    }
    public function groupClassReservations($id)
    {
        $reservations = GroupClassReservation::with('user') // رابطه با دانش‌آموز
        ->where('group_class_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.group_classes.reservations', compact('reservations'));
    }
    public function delete(GroupClass $groupClass)
    {
        $groupClass->delete();
        return redirect()->route('admin.group_class.index');


    }

}
