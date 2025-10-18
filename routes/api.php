<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\Certificate\CertificateController;
use App\Http\Controllers\Api\Chat\MassageController;
use App\Http\Controllers\Api\Class\GroupClassController;
use App\Http\Controllers\Api\Class\PrivateClassController;
use App\Http\Controllers\Api\Class\WebinarClassController;
use App\Http\Controllers\Api\Comment\UserCommentController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\Exam\ExamController;
use App\Http\Controllers\Api\Interest\InterestController;
use App\Http\Controllers\Api\library\BookController;
use App\Http\Controllers\Api\MainPageController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\Plans\UserPlansController;
use App\Http\Controllers\Api\Professor\DashboardController;
use App\Http\Controllers\Api\Professor\ProfessorController;
use App\Http\Controllers\Api\Professor\ProfessorWebinarController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\Students\StudentController;
use App\Http\Controllers\Api\Students\WebinarController;
use App\Http\Controllers\Api\Ticket\TicketController;
use App\Http\Controllers\Api\Wllet\WalletController;
use App\Mail\OtpMail;

//Route::middleware('frontend.secret')->group(function () {


Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::get('/messages/{receiverId}', [MassageController::class, 'getMessagesWithUser']);
    Route::get('/my-chats', [MassageController::class, 'getMyChats']);
    Route::get('/chat-info/{receiverId}', [MassageController::class, 'chat_info']);
    Route::post('/send-message/{receiverId}', [MassageController::class, 'sendMessage']);
});

Route::get('/chat/user-details/{id}', [MassageController::class, 'user_details']);
Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->middleware('auth:sanctum');
Route::get('/student/home_work', [StudentDashboardController::class, 'home_work'])->middleware('auth:sanctum');


Route::get('/main', [MainPageController::class, 'index']);
Route::get('/plans/show/{id}', [PlanController::class, 'show']);

Route::prefix('plans')->controller(App\Http\Controllers\Api\PlanController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/buy/{id}', 'buy')->middleware('auth:sanctum');
});
Route::prefix('exam')->controller(ExamController::class)->group(function () {
    Route::get('', 'exams');
    Route::get('/final', 'final');
    Route::get('/show/{id}', 'exam_show')->middleware('optional.auth');
    Route::post('/comment/{id}', 'comment')->middleware('auth:sanctum');
    Route::get('/comment/{id}', 'showComments');
    Route::post('/like/{id}', 'toggleLike')->middleware('auth:sanctum');
    Route::post('/rate/{id}', 'rate')->middleware('auth:sanctum');
    Route::post('/buy/{id}', 'buy_exam')->middleware('auth:sanctum');


    Route::post('/answer', 'submitAnswers')->middleware('auth:sanctum');
    Route::post('/getPartAnswers', 'getPartAnswers')->middleware('auth:sanctum');
    Route::get('/{id}', 'question')->middleware('auth:sanctum');
    Route::get('/beforeFinishExam/{id}', 'beforeFinishExam')->middleware('auth:sanctum');
    Route::post('/finishExam/{id}', 'finishExam')->middleware('auth:sanctum');

    Route::prefix('placement')->controller(\App\Http\Controllers\Api\Exam\PlacementController::class)->group(function () {
        Route::get('/info', 'info');
        Route::post('/placement', 'placement')->middleware('auth:sanctum');
    });
});

Route::get('/professors_comments/text', [UserCommentController::class, 'professors_comments'])->middleware('optional.auth');
Route::get('/professors_comments/video', [UserCommentController::class, 'professors_comments_video'])->middleware('optional.auth');
Route::get('/professors_comments/audio', [UserCommentController::class, 'professors_comments_audio'])->middleware('optional.auth');

Route::get('/getDaySlotsForAll', [App\Http\Controllers\Api\Calender\CalenderController::class, 'getDaySlotsForAll']);

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/loginOtp', 'loginOtp');
    Route::post('/sendOtp', [\App\Http\Controllers\Api\Auth\SmsController::class,'sendOtp']);
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});
Route::get('/test-email', function () {
    $testCode = ['code' => 123456]; // کد تستی

    try {
        Mail::to('aryantallaj1382@gmail.com')->send(new OtpMail($testCode));
        return 'ایمیل ارسال شد!';
    } catch (\Throwable $e) {
        return 'خطا در ارسال ایمیل: ' . $e->getMessage();
    }
});
Route::prefix('comment')->controller(CommentController::class)->middleware('auth:sanctum')->name('comment.')->group(function () {
    Route::post('/like/{id}', 'toggleLike');
    Route::post('/dislike/{id}', 'disLike');
});

Route::prefix('placement')->controller(PrivateClassController::class)->middleware('auth:sanctum')->name('comment.')->group(function () {
    Route::get('/', 'index');
    Route::get('/professor', 'professor_placement');
});

Route::prefix('story')->controller(App\Http\Controllers\Api\Story\StoryController::class)->group(function () {
    Route::get('/{id}', 'show')->middleware('optional.auth');
    Route::post('/like/{id}', 'toggleLike')->middleware('auth:sanctum');
    Route::post('/comment/{id}', 'comment')->middleware('auth:sanctum');
});
Route::prefix('class/private')->controller(PrivateClassController::class)->group(function () {
    Route::get('/', 'professors');
    Route::get('/getFilters', 'getFilters');
    Route::get('/{id}', 'showPrivate')->middleware('optional.auth');;
    Route::get('/times/{id}', 'times');
    Route::get('/details/{id}', 'details');
    Route::get('/details_placement/{id}', 'details_placement');
    Route::get('/info/{id}', 'info')->middleware('auth:sanctum');
    Route::post('/store', 'store')->middleware('auth:sanctum');
    Route::post('/final_store/{id}', 'store2')->middleware('auth:sanctum');
    Route::post('/comment/{id}', 'comment')->middleware('auth:sanctum');
    Route::get('/comment/{id}', 'showComments');
    Route::post('/like/{id}', 'toggleLike')->middleware('auth:sanctum');
    Route::post('/rate/{id}', 'rate')->middleware('auth:sanctum');
});


Route::prefix('class/group')->controller(GroupClassController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/info/{id}', 'info')->middleware('auth:sanctum');
    Route::get('/{id}', 'show')->middleware('optional.auth');;
    Route::post('/store/{id}', 'store')->middleware('auth:sanctum');
    Route::post('/comment/{id}', 'comment')->middleware('auth:sanctum');
    Route::get('/comment/{id}', 'showComments');
    Route::post('/like/{id}', 'toggleLike')->middleware('auth:sanctum');
    Route::post('/rate/{id}', 'rate')->middleware('auth:sanctum');
});

Route::prefix('class/webinar')->controller(WebinarClassController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show')->middleware('optional.auth');
    Route::get('/info/{id}', 'info')->middleware('auth:sanctum');
    Route::post('/comment/{id}', 'comment')->middleware('auth:sanctum');
    Route::get('/comment/{id}', 'showComments');
    Route::post('/store/{id}', 'store')->middleware('auth:sanctum');
    Route::post('/like/{id}', 'toggleLike')->middleware('auth:sanctum');
    Route::post('/rate/{id}', 'rate')->middleware('auth:sanctum');
});

Route::prefix('library')->controller(BookController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/getFilters', 'getFilters');
    Route::get('/{id}', 'show')->middleware('optional.auth');
    Route::post('/comment/{id}', 'comment')->middleware('auth:sanctum');
    Route::get('/comment/{id}', 'showComments');
    Route::post('/like/{id}', 'toggleLike')->middleware('auth:sanctum');
    Route::post('/rate/{id}', 'rate')->middleware('auth:sanctum');
});

Route::prefix('blog')->middleware('optional.auth')->controller(BlogController::class)->group(function () {
    Route::get('/index', 'index');
    Route::get('/index2', 'index2');
    Route::get('/show/{id}', 'show');
});

Route::prefix('student')->middleware('auth:sanctum')->controller(StudentController::class)->group(function () {
    Route::get('/', 'show');
    Route::post('/', 'store');
    Route::post('/pass', 'pass');
    Route::get('/goal', 'goal');

    Route::prefix('plans')->middleware('auth:sanctum')->controller(UserPlansController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/history', 'history');
    });
    Route::prefix('certificate')->middleware('auth:sanctum')->controller(CertificateController::class)->group(function () {
        Route::get('/', 'index');
    });
    Route::prefix('notification')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Notification\NotificationController::class)->group(function () {
        Route::get('/', 'index');
    });

    Route::prefix('wallet')->middleware('auth:sanctum')->controller(WalletController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/show', 'show');
    });
    Route::prefix('private')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Students\PrivateClassController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/cancel/{id}', 'cancel');
        Route::get('/information/{id}', 'information');
        Route::get('/sessions/{id}', 'sessions');
        Route::get('/calender/{id}', 'calender');
        Route::get('/new_class/{id}', 'new_class');
        Route::get('/home_work/{id}', 'home_work');
        Route::get('/delay/{id}', 'delay');
        Route::post('/upload_answer/{id}', 'upload_answer');
        Route::get('/activityReport/{id}', 'activityReport');
        Route::get('/report_table/{id}', 'report_table');
        Route::get('/activity/{id}', 'activity');
        Route::post('/certificate/{id}', 'certificate');
        Route::get('/certificate_show/{id}', 'certificate_show');
        Route::post('/submit/{id}', 'submit');
    });



    Route::prefix('group')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Students\GroupClassController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/cancel/{id}', 'cancel');
        Route::get('/information/{id}', 'information');
        Route::get('/sessions/{id}', 'sessions');
        Route::get('/calender/{id}', 'calender');
        Route::get('/new_class/{id}', 'new_class');
        Route::get('/home_work/{id}', 'home_work');
        Route::get('/delay/{id}', 'delay');
        Route::post('/upload_answer/{id}', 'upload_answer');
        Route::get('/activityReport/{id}', 'activityReport');
        Route::get('/report_table/{id}', 'report_table');
        Route::get('/activity/{id}', 'activity');
        Route::post('/certificate/{id}', 'certificate');
        Route::get('/certificate_show/{id}', 'certificate_show');
        Route::post('/submit/{id}', 'submit');
    });
    Route::prefix('interest')->middleware('auth:sanctum')->controller(InterestController::class)->group(function () {
        Route::get('/', 'index');
    });
    Route::prefix('webinar')->middleware('auth:sanctum')->controller(WebinarController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/information/{id}', 'information');
        Route::get('/new_class/{id}', 'new_class');
        Route::post('/certificate/{id}', 'certificate');
        Route::get('/certificate_show/{id}', 'certificate_show');
        Route::post('/submit/{id}', 'submit');

    });
    Route::prefix('ticket')->middleware('auth:sanctum')->controller(TicketController::class)->group(function () {
        Route::get('/', 'userTickets');
        Route::get('/show/{id}', 'showTicketConversation');
        Route::post('/store', 'store');
        Route::post('/add/{id}', 'addMessage');
        Route::post('/close/{id}', 'closeTicket');

    });
    Route::prefix('comment')->middleware('auth:sanctum')->controller(UserCommentController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');

    });
    Route::prefix('calender')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Calender\PanelCalenderController::class)->group(function () {
        Route::get('/monthly', 'monthlyCalendar');
        Route::get('/weekly', 'weeklyCalendar');
        Route::get('/daily', 'dailyCalendar');

    });

    Route::prefix('exam')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Students\ExamStudentController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/show/{id}', 'show');
        Route::get('/show/{id}/placement', 'show_placement');
        Route::get('/result/{id}', 'result');
        Route::get('/result/{id}/placement', 'result_placement');

    });


});


Route::prefix('professor')->middleware('auth:sanctum')->controller(StudentController::class)->group(function () {

    Route::prefix('wallet')->middleware('auth:sanctum')->controller(WalletController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/show', 'show');
    });
    Route::prefix('ticket')->middleware('auth:sanctum')->controller(TicketController::class)->group(function () {
        Route::get('/', 'userTickets');
        Route::get('/show/{id}', 'showTicketConversation');
        Route::post('/store', 'store');
        Route::post('/add/{id}', 'addMessage');
        Route::post('/close/{id}', 'closeTicket');

    });
    Route::prefix('information')->middleware('auth:sanctum')->controller(ProfessorController::class)->group(function () {
        Route::get('/', 'showPrivate');
    });
    Route::prefix('books')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Professor\ProfessorLibreryController::class)->group(function () {
        Route::get('/', 'books');
    });
    Route::prefix('notification')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Notification\NotificationController::class)->group(function () {
        Route::get('/', 'index');
    });
    Route::prefix('calender')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Calender\ProfessorPanelCalenderController::class)->group(function () {
        Route::get('/monthly', 'monthlyCalendar');
        Route::get('/weekly', 'weeklyCalendar');
        Route::get('/daily', 'dailyCalendar');

    });
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/get_user', [ProfessorController::class, 'get_user']);


    Route::prefix('private')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Professor\ProfessorPrivateClassController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/cancel/{id}', 'cancel');
        Route::get('/information/{id}', 'information');
        Route::get('/sessions/{id}', 'sessions');
        Route::get('/calender/{id}', 'calender');
        Route::get('/new_class/{id}', 'new_class');
        Route::post('/report', 'report');

    });
    Route::prefix('group')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Students\GroupClassController::class)->group(function () {
        Route::get('/', 'index');
//        Route::post('/cancel/{id}', 'cancel');
        Route::get('/information/{id}', 'information');
        Route::get('/sessions/{id}', 'sessions');
        Route::get('/calender/{id}', 'calender');
        Route::get('/new_class/{id}', 'new_class');
//
    });

    Route::prefix('webinar')->middleware('auth:sanctum')->controller(ProfessorWebinarController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/information/{id}', 'information');
        Route::get('/new_class/{id}', 'new_class');


    });
});




//});
