<?php

namespace App\Http\Controllers\Admin;

use App\Models\AgeGroup;
use App\Models\Book;
use App\Models\GroupClass;
use App\Models\GroupClassReservation;
use App\Models\Language;
use App\Models\LanguageLevel;
use App\Models\LearningSubgoal;
use App\Models\Platform;
use App\Models\Professor;
use App\Models\Webinar;
use App\Models\WebinarReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminWebinarController extends Collection
{
    public function index()
    {
        $classes = Webinar::with('subject')->latest()->paginate(12);
        return view('admin.webinar.index', compact('classes'));
    }
    public function show($id)
    {
        $class = Webinar::with(['professor.user', 'language', 'languageLevel', 'ageGroup', 'platform'])
            ->findOrFail($id);

        return view('admin.webinar.show', compact('class'));
    }
    public function create()
    {
        $professors = Professor::all();
        $languages = Language::all();
        $platforms = Platform::all();
        $subjects = LearningSubgoal::all();
        $books = Book::all();
        $ageGroups = AgeGroup::all();
        $languageLevels = LanguageLevel::all();



        return view('admin.webinars.create', compact(
            'professors', 'languages', 'platforms', 'subjects', 'books' , 'ageGroups' , 'languageLevels'
        ));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'professor_id' => 'required|exists:professors,id',
            'age_group_id' => 'nullable|integer',
            'language_level_id' => 'nullable|integer',
            'subject_id' => 'required|exists:learning_subgoals,id',
            'language_id' => 'required|exists:languages,id',
            'platform_id' => 'nullable|exists:platforms,id',
            'book_id' => 'nullable|exists:books,id',
            'min_capacity' => 'nullable|integer|min:1',
            'max_capacity' => 'nullable|integer|min:1',
            'date' => 'required|date',
            'time' => 'required',
            'image' => 'nullable|image|max:2048',
            'class_link' => 'nullable|string',
            'admin_status' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('webinars', 'public');
        }

        Webinar::create($data);

        return redirect()->route('admin.webinar.index')->with('success', 'وبینار با موفقیت ایجاد شد');
    }

    public function edit(Webinar $webinar)
    {
        $professors = Professor::all();
        $languages = Language::all();
        $platforms = Platform::all();
        $subjects = LearningSubgoal::all();
        $books = Book::all();
        $ageGroups = AgeGroup::all();
        $languageLevels = LanguageLevel::all();

        return view('admin.webinars.edit', compact(
            'webinar',
            'professors', 'languages', 'platforms', 'subjects', 'books', 'ageGroups', 'languageLevels'
        ));
    }


    public function update(Request $request, Webinar $webinar)
    {
        $data = $request->validate([
            'professor_id' => 'required|exists:professors,id',
            'age_group_id' => 'nullable|integer',
            'language_level_id' => 'nullable|integer',
            'subject_id' => 'required|exists:learning_subgoals,id',
            'language_id' => 'required|exists:languages,id',
            'platform_id' => 'nullable|exists:platforms,id',
            'book_id' => 'nullable|exists:books,id',
            'min_capacity' => 'nullable|integer|min:1',
            'max_capacity' => 'nullable|integer|min:1',
            'date' => 'required|date',
            'time' => 'required',
            'image' => 'nullable|image|max:2048',
            'class_link' => 'nullable|string',
            'admin_status' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // حذف تصویر قبلی در صورت وجود
            if ($webinar->image && file_exists(public_path('storage/' . str_replace('public/', '', $webinar->image)))) {
                unlink(public_path('storage/' . str_replace('public/', '', $webinar->image)));
            }
            $data['image'] = $request->file('image')->store('webinars', 'public');
        }

        $webinar->update($data);

        return redirect()->route('admin.webinar.index')->with('success', 'وبینار با موفقیت ویرایش شد');
    }


    public function destroy(Webinar $webinar)
    {
        $webinar->delete();
        return redirect()->route('admin.webinar.index')->with('success', 'وبینار حذف شد');
    }

    public function groupClassReservations($id)
    {
        $reservations = WebinarReservation::with('user') // رابطه با دانش‌آموز
        ->where('webinar_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.webinar.reservations', compact('reservations'));
    }
}
