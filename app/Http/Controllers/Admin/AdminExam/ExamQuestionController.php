<?php

namespace App\Http\Controllers\Admin\AdminExam;

use App\Http\Controllers\Controller;
use App\Models\ExamPart;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamQuestionController extends Controller
{

    // لیست سوالات
    public function index(Request $request, $partId)
    {
        $part = ExamPart::findOrFail($partId);
        $uniqueTitles = ExamQuestion::whereNotNull('title')
            ->where(function($q){
                $q->where('question_type','blank')
                    ->orWhere('question_type','test');
            })
            ->when($request->filled('type'), function($q) use ($request) {
                $q->where('question_type', $request->type);
            })
            ->when($request->filled('search'), function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->select('title', DB::raw('MIN(id) as min_id'))
            ->groupBy('title');

        // گرفتن رکوردهای کامل بر اساس min_id
        $questionsBank = ExamQuestion::with(['variants.options','media'])
            ->whereIn('id', $uniqueTitles->pluck('min_id'))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->all()); // 👈 حفظ پارامترهای پیجینیشن و فیلتر

        // سوالات خود پارت
        $questions = $part->questions()->with('variants', 'media')->get();

        return view('admin.exam_questions.index', compact('part', 'questions', 'questionsBank'));
    }






    // ذخیره سوال جدید
    public function store(Request $request, $examPartId)
    {
        $part = ExamPart::findOrFail($examPartId);

        // پیدا کردن آخرین شماره موجود برای این پارت
        $lastNumber = $part->questions()->max('number');
        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        $request->validate([
            // دیگر نیازی به شماره از فرم نیست، چون اتوماتیک می‌شود
            'question_type' => 'required',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'question' => 'nullable|string',

            'variants.*.question' => 'required_if:question_type,test|string|max:255',
            'variants.*.options' => 'required_if:question_type,test|array',
            'variants.*.options.*.text' => 'required|string|max:255',
            'variants.*.options.*.is_correct' => 'nullable|boolean',

            'media.*' => 'nullable|file',
        ]);

        // ایجاد سوال با شماره اتوماتیک
        $question = $part->questions()->create([
            'number' => $nextNumber, // شماره اتوماتیک
            'exams_id' => $part->exam_id,
            'question_type' => $request->question_type,
            'title' => $request->title,
            'description' => $request->description,
            'question' => $request->question,
        ]);

        // ذخیره ورینت و آپشن‌ها (فقط برای تستی)
        if ($request->question_type == 'test' && $request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $variant = $question->variants()->create([
                    'text' => $variantData['question'],
                ]);

                if (isset($variantData['options']) && is_array($variantData['options'])) {
                    foreach ($variantData['options'] as $opt) {
                        $variant->options()->create([
                            'text'       => $opt['text'],
                            'is_correct' => isset($opt['is_correct']) ? 1 : 0,
                        ]);
                    }
                }
            }
        }

        // ذخیره فایل‌ها
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $destination = public_path('storage/exam_questions');
                $file->move($destination, $filename);

                $question->media()->create([
                    'path' => 'exam_questions/' . $filename,
                    'description' => $request->media_description[$index] ?? '',
                ]);
            }
        }

        return redirect()->route('admin.exam_questions.index', $examPartId)
            ->with('success', 'سوال با موفقیت ایجاد شد.');
    }

    // ویرایش

    // حذف
    public function destroy($examPartId, $id)
    {
        $part = ExamPart::findOrFail($examPartId);
        $question = $part->questions()->findOrFail($id);
        $question->delete();

        return redirect()->route('admin.exam_questions.index', $examPartId)
            ->with('success', 'سوال با موفقیت حذف شد.');
        }



    public function q_edit($id)
    {
        $question = ExamQuestion::with('variants.options')->findOrFail($id);

        return view('admin.exam_questions.update', compact('question'));
    }

    public function q_update(Request $request, $id)
    {
        $question = ExamQuestion::findOrFail($id);

        $request->validate([
            'number' => 'required|integer',
            'question_type' => 'required',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'question' => 'nullable|string',

            'variants.*.question' => 'required_if:question_type,test|string|max:255',
            'variants.*.options' => 'required_if:question_type,test|array',
            'variants.*.options.*.text' => 'required|string|max:255',
            'variants.*.options.*.is_correct' => 'nullable|boolean',
        ]);

        // آپدیت اطلاعات اصلی سوال
        $question->update([
            'number' => $request->number,
            'question_type' => $request->question_type,
            'title' => $request->title,
            'description' => $request->description,
            'question' => $request->question,
        ]);

        // پاک کردن واریانت‌های قدیمی و ذخیره‌ی جدیدها
        $question->variants()->delete();

        if ($request->question_type == 'test' && $request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $variant = $question->variants()->create([
                    'text' => $variantData['question'],
                ]);

                if (isset($variantData['options']) && is_array($variantData['options'])) {
                    foreach ($variantData['options'] as $opt) {
                        $variant->options()->create([
                            'text'       => $opt['text'],
                            'is_correct' => isset($opt['is_correct']) ? 1 : 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.exam_questions.index', $question->exam_part_id)
            ->with('success', 'سوال با موفقیت ویرایش شد.');
    }




    public function clone($partId, $questionId)
    {
        $question = ExamQuestion::with(['variants.options', 'media'])->findOrFail($questionId);

        // پیدا کردن آخرین شماره موجود برای این پارت
        $lastNumber = ExamQuestion::where('exam_part_id', $partId)->max('number');
        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        // کپی سوال
        $newQuestion = $question->replicate();
        $newQuestion->exam_part_id = $partId;
        $newQuestion->number = $nextNumber; // شماره جدید یکتا
        $newQuestion->save();

        // کپی ورینت‌ها و آپشن‌ها
        foreach ($question->variants as $variant) {
            $newVariant = $variant->replicate();
            $newVariant->exam_question_id = $newQuestion->id;
            $newVariant->save();

            foreach ($variant->options as $option) {
                $newOption = $option->replicate();
                $newOption->exam_variant_id = $newVariant->id;
                $newOption->save();
            }
        }

        // کپی فایل‌ها
        foreach ($question->media as $media) {
            $newMedia = $media->replicate();
            $newMedia->exam_question_id = $newQuestion->id;
            $newMedia->save();
        }

        return redirect()->back()->with('success', 'سوال با موفقیت از بانک سوالات انتخاب و اضافه شد.');
    }



}
