<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBlogsController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminCertificateController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminExam\ExamController;
use App\Http\Controllers\Admin\AdminExam\ExamQuestionController;
use App\Http\Controllers\Admin\AdminGroupClassController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminPrivateClassListController;
use App\Http\Controllers\Admin\AdminProfessorBookController;
use App\Http\Controllers\Admin\AdminProfessorController;
use App\Http\Controllers\Admin\AdminProfessorStoryController;
use App\Http\Controllers\Admin\AdminSliderController;
use App\Http\Controllers\Admin\AdminStoryController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminWebinarController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\BlogController;
use Illuminate\Support\Facades\Route;



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
Route::middleware('guest:web')->group(function() {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    });
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('welcome');
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{id}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');

    Route::delete('/stories/{story}', [AdminStoryController::class, 'destroy'])->name('stories.destroy');

    Route::get('/stories/main-page', [AdminStoryController::class, 'mainPageStories'])->name('stories.main');
    Route::get('/stories/create', [AdminStoryController::class, 'create'])->name('stories.create');
    Route::post('/stories', [AdminStoryController::class, 'store'])->name('stories.store');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    // فرم ایجاد آزمون
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');

    Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{id}/parts', [ExamController::class, 'storePart'])->name('exams.parts.store');

    Route::resource('blogs', AdminBlogsController::class);
    Route::resource('books', AdminBookController::class);
    Route::resource('plans', AdminPlanController::class);
    Route::get('Plans/{plan}/users', [AdminPlanController::class, 'users'])->name('plans.users');
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('', [AdminCertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}/edit', [AdminCertificateController::class, 'edit'])->name('edit');
        Route::post('/{certificate}', [AdminCertificateController::class, 'update'])->name('update');
    });

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/exams/{exam}/parts/{part}', [ExamController::class, 'update_part'])->name('exams.parts.update');
    Route::patch('transactions/{transaction}/status', [UserController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::post('/users/{user}/wallet', [UserController::class, 'update'])->name('users.wallet.update');
    Route::delete('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

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


    Route::prefix('professors')->name('professors.')->group(function () {
        Route::get('/', [AdminProfessorController::class, 'index'])->name('index');
        Route::get('/create', [AdminProfessorController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [AdminProfessorController::class, 'edit'])->name('edit');
        Route::post('/store', [AdminProfessorController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminProfessorController::class, 'show'])->name('show');
        Route::put('/update/{professor}', [AdminProfessorController::class, 'update'])->name('update');

    });
    Route::prefix('admin/professors/{professor}')->name('professorsStory.')->group(function () {
        Route::get('stories', [AdminProfessorStoryController::class, 'index'])->name('index');
        Route::post('stories', [AdminProfessorStoryController::class, 'store'])->name('store');
        Route::delete('stories/{story}', [AdminProfessorStoryController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('professorsBook')->name('professorsBook.')->group(function () {
        Route::get('/{id}', [AdminProfessorBookController::class, 'index'])->name('index');
        Route::post('/store/{id}', [AdminProfessorBookController::class, 'update'])->name('update');


    });


    Route::prefix('group_class')->name('group_class.')->group(function () {
        Route::get('/', [AdminGroupClassController::class, 'index'])->name('index');
        Route::get('/groupClassReservations/{id}', [AdminGroupClassController::class, 'groupClassReservations'])->name('groupClassReservations');
        Route::get('/show/{id}', [AdminGroupClassController::class, 'show'])->name('show');
        Route::get('/create', [AdminGroupClassController::class, 'create'])->name('create');
        Route::post('/store', [AdminGroupClassController::class, 'store'])->name('store');
        Route::get('/edit/{groupClass}', [AdminGroupClassController::class, 'edit'])->name('edit');
        Route::put('/update/{groupClass}', [AdminGroupClassController::class, 'update'])->name('update');
        Route::put('/updateSchedule/{schedule}', [AdminGroupClassController::class, 'updateSchedule'])->name('updateSchedule');


    });
    Route::prefix('webinar')->name('webinar.')->group(function () {
        Route::get('/', [AdminWebinarController::class, 'index'])->name('index');
        Route::get('/show/{id}', [AdminWebinarController::class, 'show'])->name('show');
        Route::get('/groupClassReservations/{id}', [AdminWebinarController::class, 'groupClassReservations'])->name('groupClassReservations');

        Route::get('/create', [AdminWebinarController::class, 'create'])->name('create');
        Route::post('/store', [AdminWebinarController::class, 'store'])->name('store');
        Route::get('/edit/{webinar}', [AdminWebinarController::class, 'edit'])->name('edit');
        Route::put('/update/{webinar}', [AdminWebinarController::class, 'update'])->name('update');
        Route::delete('/webinar/{webinar}', [AdminWebinarController::class, 'destroy'])->name('destroy');


    });
    Route::get('/sliders', [AdminSliderController::class, 'index'])->name('sliders.index');
    Route::post('/sliders', [AdminSliderController::class, 'store'])->name('sliders.store');
    Route::delete('/sliders/{id}', [AdminSliderController::class, 'destroy'])->name('sliders.destroy');



    Route::prefix('private_list')->name('private-classes.')->group(function () {
        Route::get('/', [AdminPrivateClassListController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPrivateClassListController::class, 'show'])->name('show');

        Route::post('update-link/{id}', [AdminPrivateClassListController::class, 'updateClassLink'])
            ->name('update-link');


    });

});
