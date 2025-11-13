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
        $level= AgeGroup::all();
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|string|max:20',
            'bio' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
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
            $profile = 'profile' . $fileName;
        }
        $user = User::create([
            'first_name' => $request->name,
            'email' => $request->email,
            'profile' => $profile ?? null,
            'password' => bcrypt($request->password),
        ]);


        $professor = Professor::create([
            'user_id' => $user->id,
            'bio' => $request->bio,
            'name' => $request->name,
            'phone' => $request->mobile,
            'years_of_experience' => $request->years_of_experience,
            'is_active' => $request->boolean('is_active'),
            'is_verified' => $request->boolean('is_verified'),
            'is_native' => $request->boolean('is_native'),
            'gender' => $request->gender,
            'birth_date' => $request->birthdate,
            'placement' => $request->placement,
            'trial' => $request->trial,
        ]);


        // آپلود فایل‌ها
        if ($request->hasFile('video_1')) {
            $professor->video_1 = $request->file('video_1')->store('professors/videos', 'public');
        }
        if ($request->hasFile('video_2')) {
            $professor->video_2 = $request->file('video_2')->store('professors/videos', 'public');
        }
        if ($request->hasFile('image_1')) {
            $professor->image_1 = $request->file('image_1')->store('professors/images', 'public');
        }
        if ($request->hasFile('image_2')) {
            $professor->image_2 = $request->file('image_2')->store('professors/images', 'public');
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
            'password' => 'nullable',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'sample_video' => 'nullable|mimetypes:video/mp4,video/mov,video/avi|max:20000',
            'teaching_video' => 'nullable|mimetypes:video/mp4,video/mov,video/avi|max:20000',
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
        ]);

        if ($request->hasFile('profile')) {
            if ($professor->user->profile) {
                Storage::disk('public')->delete($professor->user->profile);
            }
            $path = $request->file('profile')->store('professors/profile', 'public');
            $professor->user->update(['profile' => $path]);
        }
        if ($request->hasFile('sample_video')) {
            if ($professor->sample_video) {
                Storage::disk('public')->delete($professor->sample_video);
            }
            $professor->update([
                'sample_video' => $request->file('sample_video')->store('professors/videos', 'public')
            ]);
        }
        if ($request->hasFile('teaching_video')) {
            if ($professor->teaching_video) {
                Storage::disk('public')->delete($professor->teaching_video);
            }
            $professor->update([
                'teaching_video' => $request->file('teaching_video')->store('professors/videos', 'public')
            ]);
        }
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


}
