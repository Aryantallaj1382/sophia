<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accent;
use App\Models\AgeGroup;
use App\Models\Book;
use App\Models\LanguageLevel;
use App\Models\LearningGoal;
use App\Models\Platform;
use App\Models\Professor;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfessorController extends Controller
{
    public function index(Request $request)
    {
        $query = Professor::query();

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('id', $search) // بررسی id استاد
                ->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            });
        }

        // مرتب‌سازی
        $sortBy = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sortBy, $direction);

        // پیجینیشن
        $professors = $query->paginate(10);

        return view('admin.professor.index', compact('professors'));
    }
    // فرم ایجاد استاد
    public function create()
    {
        $age = AgeGroup::all();
        $level= LanguageLevel::all();
        $platform= Platform::all();
        $accent= Accent::all();
        $skill= Skill::all();
        $book= Book::all();
        $goals = LearningGoal::with('subgoals')->get();
        return view('admin.professor.create' , compact(['age', 'level','platform', 'accent' , 'skill', 'book' , 'goals']));
    }

    public function show($id)
    {
        $professor = Professor::with([
            'ageGroups',
            'languageLevels',
            'platforms',
            'accents',
            'story',
            'skills',
            'books',
            'learningGoals.subgoal',
        ])->findOrFail($id);


        return view('admin.professor.show', compact('professor'));
    }

// ذخیره استاد جدید
    public function store(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|string|max:20',
            'bio' => 'nullable|string',
            'years_of_experience' => 'nullable',
            'is_active' => 'nullable',
            'is_verified' => 'nullable',
            'is_native' => 'nullable',
            'gender' => 'required|in:male,female',
            'birth_date' => 'nullable|date',
            'placement' => 'nullable|in:null,0,50,100',
            'trial' => 'nullable|in:null,0,50,100',


            'sample_video' => 'nullable|file|mimes:mp4,mov,avi|max:51200', // 50MB
            'teaching_video' => 'nullable|file|mimes:mp4,mov,avi|max:51200',
            'sample_video_cover' => 'nullable', // 10MB
            'profile' => 'nullable', // 10MB
            'teaching_video_cover' => 'nullable',
        ]);

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $destinationPath = public_path('profile');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $profile = 'profile/' . $fileName;
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'profile' => $profile ?? null,
            'password' => bcrypt($request->password),
        ]);


        $professor = Professor::create([
            'user_id' => $user->id,
            'bio' => $request->bio,
            'name' => $request->first_name.' '.$request->last_name,
            'phone' => $request->mobile,
            'years_of_experience' => $request->years_of_experience,
            'is_active' => $request->boolean('is_active'),
            'is_verified' => $request->boolean('is_verified'),
            'is_native' => $request->boolean('is_native'),
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'placement' => $request->placement,
            'trial' => $request->trial,
        ]);


        if ($request->hasFile('sample_video')) {
            $file = $request->file('sample_video');
            $destinationPath = public_path('professors/sample_video');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->sample_video = 'professors/sample_video/' . $fileName;
        }

        if ($request->hasFile('teaching_video')) {
            $file = $request->file('teaching_video');
            $destinationPath = public_path('professors/teaching_video');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->teaching_video = 'professors/teaching_video/' . $fileName;
        }

        if ($request->hasFile('sample_video_cover')) {
            $file = $request->file('sample_video_cover');
            $destinationPath = public_path('professors/images');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->sample_video_cover = 'professors/images/' . $fileName;
        }

        if ($request->hasFile('teaching_video_cover')) {
            $file = $request->file('teaching_video_cover');
            $destinationPath = public_path('professors/images');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->teaching_video_cover = 'professors/images/' . $fileName;
        }


        $professor->save();
        $user->save();

        return redirect()->back()->with('success', 'استاد با موفقیت ثبت شد.');
    }


    public function edit($id)
    {
        $professor = Professor::with('user')->findOrFail($id);

        $age = AgeGroup::all();
        $level = LanguageLevel::all();
        $platform = Platform::all();
        $accent = Accent::all();
        $skill = Skill::all();
        $book = Book::all();
        $goals = LearningGoal::with('subgoals')->get();

        return view('admin.professor.edit', compact('professor', 'age', 'level', 'platform', 'accent', 'skill', 'book', 'goals'));
    }

    public function update(Request $request, Professor $professor)
    {
        // اعتبارسنجی پایه
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $professor->user_id,
            'mobile' => 'nullable|string|max:20',
            'years_of_experience' => 'nullable|integer|min:0',
            'bio' => 'nullable|string',
            'trial' => 'nullable|string',
            'voov_link' => 'nullable|string',
            'classin_link' => 'nullable|string',
            'zoom_link' => 'nullable|string',
            'teams' => 'nullable|string',
            'placement' => 'nullable|string',
            'password' => 'nullable',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'sample_video' => 'nullable',
            'teaching_video' => 'nullable',
            'sample_video_cover' => 'nullable',
            'teaching_video_cover' => 'nullable',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $professor->user->update($userData);
        $professor->update([
            'years_of_experience' => $request->years_of_experience,
            'bio' => $request->bio,
            'is_active' => $request->has('is_active'),
            'is_verified' => $request->has('is_verified'),
            'is_native' => $request->has('is_native'),
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'placement' => $request->placement,
            'trial' => $request->trial,
            'teams' => $request->teams,
            'zoom_link' => $request->zoom_link,
            'classin_link' => $request->classin_link,
            'voov_link' => $request->voov_link,
        ]);

        // حذف عکس پروفایل
        if ($request->has('delete_profile') && $request->delete_profile == '1') {
            if ($professor->user->profile && file_exists(public_path($professor->user->profile))) {
                unlink(public_path($professor->user->profile));
            }
            $professor->user->update(['profile' => null]);
        }

// پروفایل
        if ($request->hasFile('profile')) {
            $file = $request->file('profile');

            if ($professor->user->profile && file_exists(public_path($professor->user->profile))) {
                unlink(public_path($professor->user->profile));
            }
            $destinationPath = public_path('professors/profile');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->user->update(['profile' => 'professors/profile/' . $fileName]);
        }

        // حذف ویدیو نمونه تدریس
        if ($request->has('delete_sample_video') && $request->delete_sample_video == '1') {
            if ($professor->sample_video && file_exists(public_path($professor->sample_video))) {
                unlink(public_path($professor->sample_video));
            }
            $professor->sample_video = null;
        }

// ویدیو نمونه تدریس
        if ($request->hasFile('sample_video')) {
            $file = $request->file('sample_video');

            if ($professor->sample_video && file_exists(public_path($professor->sample_video))) {
                unlink(public_path($professor->sample_video));
            }

            $destinationPath = public_path('professors/sample_video');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->sample_video = 'professors/sample_video/' . $fileName;
        }

        // حذف ویدیو معرفی
        if ($request->has('delete_teaching_video') && $request->delete_teaching_video == '1') {
            if ($professor->teaching_video && file_exists(public_path($professor->teaching_video))) {
                unlink(public_path($professor->teaching_video));
            }
            $professor->teaching_video = null;
        }

// ویدیو معرفی
        if ($request->hasFile('teaching_video')) {
            $file = $request->file('teaching_video');

            if ($professor->teaching_video && file_exists(public_path($professor->teaching_video))) {
                unlink(public_path($professor->teaching_video));
            }

            $destinationPath = public_path('professors/teaching_video');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->teaching_video = 'professors/teaching_video/' . $fileName;
        }

        // حذف کاور ویدیوی نمونه تدریس
        if ($request->has('delete_sample_video_cover') && $request->delete_sample_video_cover == '1') {
            if ($professor->sample_video_cover && file_exists(public_path($professor->sample_video_cover))) {
                unlink(public_path($professor->sample_video_cover));
            }
            $professor->sample_video_cover = null;
        }

// کاور ویدیوی نمونه تدریس
        if ($request->hasFile('sample_video_cover')) {
            $file = $request->file('sample_video_cover');

            if ($professor->sample_video_cover && file_exists(public_path($professor->sample_video_cover))) {
                unlink(public_path($professor->sample_video_cover));
            }

            $destinationPath = public_path('professors/images');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->sample_video_cover = 'professors/images/' . $fileName;
        }

        // حذف کاور ویدیوی معرفی
        if ($request->has('delete_teaching_video_cover') && $request->delete_teaching_video_cover == '1') {
            if ($professor->teaching_video_cover && file_exists(public_path($professor->teaching_video_cover))) {
                unlink(public_path($professor->teaching_video_cover));
            }
            $professor->teaching_video_cover = null;
        }

// کاور ویدیوی معرفی
        if ($request->hasFile('teaching_video_cover')) {
            $file = $request->file('teaching_video_cover');

            if ($professor->teaching_video_cover && file_exists(public_path($professor->teaching_video_cover))) {
                unlink(public_path($professor->teaching_video_cover));
            }

            $destinationPath = public_path('professors/images');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $professor->teaching_video_cover = 'professors/images/' . $fileName;
        }

        $professor->save();


        $professor->ageGroups()->sync($request->input('age_groups', []));
        $professor->languageLevels()->sync($request->input('level', []));
        $professor->platforms()->sync($request->input('platform', []));
        $professor->accents()->sync($request->input('accent', []));
        $professor->skills()->sync($request->input('skill', []));
        $professor->learningGoals()->delete(); // حذف قبلی
        foreach ($request->input('learning_goals', []) as $subgoalId) {
            $professor->learningGoals()->create([
                'subgoal_id' => $subgoalId,
            ]);
        }
        return redirect()->route('admin.professors.edit', $professor)->with('success', 'اطلاعات استاد با موفقیت به‌روزرسانی شد.');
    }
    public function destroy($id)
    {
        // پیدا کردن استاد
        $professor = Professor::findOrFail($id);

        // حذف فایل‌های آپلود شده اگر وجود داشته باشند
        $files = [
            $professor->profile ?? null,
            $professor->sample_video ?? null,
            $professor->teaching_video ?? null,
            $professor->sample_video_cover ?? null,
            $professor->teaching_video_cover ?? null,
        ];

        foreach ($files as $file) {
            if ($file && file_exists(public_path($file))) {
                @unlink(public_path($file));
            }
        }

        // حذف کاربر مرتبط
        $user = $professor->user;
        $professor->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->back()->with('success', 'استاد با موفقیت حذف شد.');
    }

}
