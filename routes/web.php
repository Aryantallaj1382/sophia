<?php

use App\Http\Controllers\AdminExam\ExamController;
use App\Http\Controllers\AdminExam\ExamQuestionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mt', function () {
    Artisan::Call('migrate', ['--force' => true]);
    dd(Artisan::output());
});
Route::get('/optimize', function () {
    Artisan::call('optimize');
    dd(Artisan::output());
});
//Route::get('/all-otp-cache', function () {
//    $rows = DB::table('cache')->get();
//
//    $otps = [];
//
//    foreach ($rows as $row) {
//        $key = $row->key;
//
//        // فقط کلیدهای OTP را انتخاب کن
//        if (Str::startsWith($key, 'code:')) {
//            // unserialize محتوا
//            $value = unserialize($row->value);
//            $otps[$key] = $value;
//        }
//    }
//
//    return response()->json($otps);
//});
//
//Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
//Route::post('login', [LoginController::class, 'login']);
//Route::post('logout', [LoginController::class, 'logout'])->name('logout');
//Route::get('/', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});
//
//require __DIR__.'/auth.php';
Route::prefix('admin')->name('admin.')->group(function () {
    // لیست آزمون‌ها
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    // فرم ایجاد آزمون
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');

    Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{id}/parts', [ExamController::class, 'storePart'])->name('exams.parts.store');


    // ویرایش بخش
    Route::put('/exams/{exam}/parts/{part}', [ExamController::class, 'update_part'])
        ->name('exams.parts.update');

    // حذف بخش
    Route::delete('/exams/{exam}/parts/{part}', [ExamController::class, 'destroy_part'])
        ->name('exams.parts.destroy');
    Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');

    Route::get('/exams/{id}/edit', [ExamController::class, 'edit'])->name('exams.edit');

    // بروزرسانی آزمون
    Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exams.update');

    // حذف آزمون
    Route::delete('/exams/{id}', [ExamController::class, 'destroy'])->name('exams.destroy');

// web.php
    Route::get('/exams/{exam}/students', [ExamController::class, 'showStudents'])->name('exams.students');

    Route::put('questions/update/{id}', [ExamQuestionController::class, 'q_update'])->name('q_update');
    Route::get('questions/edit/{id}', [ExamQuestionController::class, 'q_edit'])->name('q_edit');
    Route::post('exam_questions/clone/{part}/{question}', [ExamQuestionController::class, 'clone'])
        ->name('exam_questions.clone');


    Route::prefix('exam-parts/{examPartId}/questions')->name('exam_questions.')->group(function () {
        Route::get('/', [ExamQuestionController::class, 'index'])->name('index');
        Route::post('/', [ExamQuestionController::class, 'store'])->name('store');

        Route::delete('/{questionId}', [ExamQuestionController::class, 'destroy'])->name('destroy');
    });

});
