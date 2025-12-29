<?php

namespace App\Http\Controllers\Admin\AdminExam;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Models\Book;
use App\Models\Exam;
use App\Models\ExamPart;
use App\Models\ExamPartMedia;
use App\Models\ExamPartType;
use App\Models\ExamQuestion;
use App\Models\ExamStudent;
use App\Models\LanguageLevel;
use App\Models\Skill;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function updateScore(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $examStudent = ExamStudent::findOrFail($id);
        $examStudent->score = $request->score;
        $examStudent->save();

        return response()->json([
            'message' => 'نمره با موفقیت بروزرسانی شد.',
            'score'   => $examStudent->score
        ]);
    }

    public function studentAnswers($exam_id, $student_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $student = Student::findOrFail($student_id);
        $questions = ExamQuestion::with([
            'variants.options',
            'answers' => function ($q) use ($student_id) {
                $q->where('student_id', $student_id)
                    ->with(['options.option', 'variant', 'student']);
            },
        ])
            ->where('exams_id', $exam_id)
            ->get();

        return view('admin.exam.student_answers', compact('exam', 'student', 'questions'));
    }


    public function index()
    {
        $exams = Exam::latest()->paginate(10);
        return view('admin.exam.index', compact(['exams']));
    }
    public function create()
    {
        $ageGroups = AgeGroup::all();
        $languageLevels = LanguageLevel::all();
        $skills  = Skill::all();
        $books = Book::all();


        return view('admin.exam.create' , compact(['ageGroups', 'languageLevels', 'skills' , 'books']));
    }
    public function show($id)
    {
        $exam = Exam::with('parts.type')->findOrFail($id);
        $partTypes = ExamPartType::all(); // برای انتخاب نوع بخش در فرم

        // بانک مدیا: ترکیب مدیاهای بخش‌ها و سوالات
        $mediaBank = ExamPartMedia::all();
        return view('admin.exam.show', compact('exam', 'partTypes', 'mediaBank'));
    }

    public function store(Request $request)
    {
        // اعتبارسنجی پایه
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'expiration' => 'nullable|string|max:255',
            'number_of_attempts' => 'nullable|integer|min:1',
            'number_of_sections' => 'nullable|integer|min:1',
            'duration' => 'nullable|integer|min:1',
            'view' => 'nullable|string|max:255',
            'type' => 'required|in:mock,final,placement',
        ]);

        $data = $request->only([
            'name',
            'description',
            'expiration',
            'number_of_attempts',
            'number_of_sections',
            'duration',
            'view',
            'type'
        ]);

        // اگر نوع آزمون تعیین سطح است، فیلدهای اضافی را اعتبارسنجی کنیم
        if ($request->type === 'placement') {
            $request->validate([
                'age_group_id' => 'required|exists:age_groups,id',
                'language_level_id' => 'required|exists:language_levels,id',
                'skill_id' => 'required',
            ]);

            // بررسی تکراری بودن
            $exists = Exam::where('type', 'placement')
                ->where('age_group_id', $request->age_group_id)
                ->where('language_level_id', $request->language_level_id)
                ->where('skill_id', $request->skill_id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['duplicate' => 'آزمون تعیین سطح با همین گروه سنی، سطح زبان و مهارت قبلاً ایجاد شده است.']);
            }

            $data['age_group_id'] = $request->age_group_id;
            $data['language_level_id'] = $request->language_level_id;
            $data['skill_id'] = $request->skill_id;
        }
        if ($request->type === 'final')
        {
            $request->validate([
                'books_id' => 'required',
            ]);
            $data['books_id'] = $request->books_id;


        }


        // تبدیل مدت زمان به فرمت HH:MM:SS
        if (!empty($data['duration'])) {
            $minutes = (int) $data['duration'];
            $hours = floor($minutes / 60);
            $mins  = $minutes % 60;
            $data['duration'] = sprintf('%02d:%02d:00', $hours, $mins);
        }

        Exam::create($data);

        return redirect()->route('admin.exams.index')->with('success', 'آزمون با موفقیت ایجاد شد.');
    }
    public function edit($id)
    {
        $ageGroups = AgeGroup::all();
        $languageLevels = LanguageLevel::all();
        $skills  = Skill::all();
        $books = Book::all();

        $exam = Exam::findOrFail($id);
        return view('admin.exam.edit', compact(['exam', 'ageGroups', 'languageLevels', 'skills' , 'books']));
    }
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        // اعتبارسنجی پایه
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'expiration' => 'nullable|string|max:255',
            'number_of_attempts' => 'nullable|integer|min:1',
            'number_of_sections' => 'nullable|integer|min:1',
            'duration' => 'nullable',
            'view' => 'nullable|string|max:255',
            'type' => 'required|in:mock,final,placement',
        ]);

        $data = $request->only([
            'name',
            'description',
            'expiration',
            'number_of_attempts',
            'number_of_sections',
            'duration',
            'view',
            'type'
        ]);

        // اگر نوع آزمون تعیین سطح است، فیلدهای اضافی را اعتبارسنجی کنیم
        if ($request->type === 'placement') {
            $request->validate([
                'age_group_id' => 'required|exists:age_groups,id',
                'language_level_id' => 'required|exists:language_levels,id',
                'skill_id' => 'required|in:listening,speaking,reading,writing',
            ]);

            $data['age_group_id'] = $request->age_group_id;
            $data['language_level_id'] = $request->language_level_id;
            $data['skill_id'] = $request->skill_id;
        } else {
            // اگر نوع دیگر است، مقادیر تعیین سطح را null کنیم
            $data['age_group_id'] = null;
            $data['language_level_id'] = null;
            $data['skill_id'] = null;
        }
        if ($request->type === 'final')
        {
            $request->validate([
                'books_id' => 'required',
            ]);
            $data['books_id'] = $request->books_id;


        }


        $exam->update($data);

        return redirect()->route('admin.exams.index')->with('success', 'آزمون با موفقیت بروزرسانی شد.');
    }
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);

        $exam->delete();

        return redirect()->route('admin.exams.index')->with('success', 'آزمون با موفقیت حذف شد.');
    }



    public function showStudents($examId)
    {
        $exam = Exam::with('students')->findOrFail($examId);

        return view('admin.exam.students', compact('exam'));
    }




    public function storePart(Request $request, $examId)
    {

        $request->validate([
            'exam_part_type_id' => 'required|exists:exam_part_types,id',
            'number' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'passenger' => 'nullable|string',
            'passenger_title' => 'nullable|string|max:255',
            'question_title' => 'nullable|string|max:255',
            'media.*' => 'nullable', // اعتبارسنجی فایل
            'media_description.*' => 'nullable|string|max:255', // توضیح مدیا
        ]);

        $exam = Exam::findOrFail($examId);

        // ایجاد بخش جدید
        $part = $exam->parts()->create($request->only([
            'exam_part_type_id',
            'number',
            'title',
            'text',
            'passenger',
            'passenger_title',
            'question_title',
        ]));


        // ذخیره مدیا انتخاب شده از بانک
        if ($request->filled('selected_media')) {
            foreach ($request->selected_media as $index => $path) {
                $description = $request->selected_media_description[$index] ?? '';
                $part->media()->create([
                    'path' => $path,
                    'description' => $description,
                ]);
            }
        }

// ذخیره فایل‌های آپلود شده
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $destination = public_path('storage/part');
                $file->move($destination, $filename);

                $part->media()->create([
                    'path' => 'part/' . $filename,
                    'description' => $request->media_description[$index] ?? '',
                ]);
            }
        }






        return redirect()->back()->with('success', 'بخش آزمون با موفقیت اضافه شد.');
    }

    public function update_part(Request $request, Exam $exam, ExamPart $part)
    {
        $request->validate([
            'exam_part_type_id' => 'required|exists:exam_part_types,id',
            'number' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'question_title' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'passenger' => 'nullable|string',
            'passenger_title' => 'nullable|string|max:255',
            'media.*' => 'nullable|file',
            'media_description.*' => 'nullable|string|max:255',
        ]);

        $part->update($request->only(
            'exam_part_type_id',
            'number',
            'title',
            'question_title',
            'text',
            'passenger',
            'passenger_title'
        ));

        // اضافه کردن فایل‌های جدید
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/exam_parts'), $filename);

                $part->media()->create([
                    'path' => 'exam_parts/' . $filename,
                    'description' => $request->media_description[$index] ?? '',
                ]);
            }
        }

        return redirect()->back()->with('success', 'بخش با موفقیت ویرایش شد.');
    }

    public function destroy_part(Exam $exam, ExamPart $part)
    {
        // حذف فایل‌های مرتبط
        foreach ($part->media as $media) {
            $filePath = public_path('storage/' . $media->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $media->delete();
        }

        $part->delete();

        return redirect()->back()->with('success', 'بخش با موفقیت حذف شد.');
    }
}
