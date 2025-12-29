<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningSubgoal;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRegisterUserController extends Controller
{

    public function edit(Student $student)
    {
        $learningSubgoals = LearningSubgoal::all();

        // آیدی ساب‌گول‌های انتخاب‌شده
        $selectedSubgoals = $student->learningSubgoals()->pluck('learning_subgoals.id')->toArray();
        return view('admin.students.edit', compact(
            'student',
            'learningSubgoals',
            'selectedSubgoals'
        ));
    }
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $student->user_id,
            'password'   => 'nullable|string|min:6',
            'birth_date' => 'nullable|date',
            'we_chat'    => 'nullable|string',
            'phone'      => 'nullable|string',
            'nickname'   => 'nullable|string',
            'profile'    => 'nullable|image',
            'learning_subgoals'   => 'array|nullable',
            'learning_subgoals.*' => 'exists:learning_subgoals,id',
        ]);

        /** ---------- Profile Image ---------- */
        $profilePath = $student->user->profile;

        if ($request->hasFile('profile')) {
            if ($profilePath && file_exists(public_path($profilePath))) {
                unlink(public_path($profilePath));
            }

            $file = $request->file('profile');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profiles'), $fileName);

            $profilePath = 'profiles/' . $fileName;
        }

        /** ---------- Update User ---------- */
        $userData = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'profile'    => $profilePath,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $student->user->update($userData);

        /** ---------- Update Student ---------- */
        $student->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'birth_date' => $request->birth_date,
            'we_chat'    => $request->we_chat,
            'phone'      => $request->phone,
            'nickname'   => $request->nickname,
        ]);

        /** ---------- Sync Goals ---------- */
        $student->learningSubgoals()->sync(
            $request->learning_subgoals ?? []
        );

        return redirect()
            ->route('admin.students.edit', $student)
            ->with('success', 'Student updated successfully!');
    }


    public function create()
    {
        $learningSubgoals = LearningSubgoal::all();

        return view('admin.students.create', compact('learningSubgoals'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'birth_date' => 'nullable|date',
            'referral_code' => 'nullable|string',
            'we_chat' => 'nullable|string',
            'phone' => 'nullable|string',
            'nickname' => 'nullable|string',
            'profile' => 'nullable',
            'learning_subgoals'   => 'array|nullable',
            'learning_subgoals.*' => 'exists:learning_subgoals,id',


        ]);
        $profilePath = null;

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('profiles'), $fileName);

            $profilePath = 'profiles/' . $fileName;
        }

        // ساخت یوزر
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'profile'    => $profilePath,

        ]);

        // ساخت دانش‌آموز
        $student = Student::create([
            'user_id'       => $user->id,
            'birth_date'    => $request->birth_date,
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'we_chat'         => $request->we_chat,
            'phone'         => $request->phone,
            'nickname'         => $request->phone,
        ]);
        if ($request->filled('learning_subgoals')) {
            $student->learningSubgoals()->sync($request->learning_subgoals);
        }

        return back()->with('success', 'Student registered successfully!');
    }


}
