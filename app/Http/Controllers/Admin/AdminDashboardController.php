<?php

namespace App\Http\Controllers\Admin;

use App\Models\GroupClassReservation;
use App\Models\PrivateClassReservation;
use App\Models\Professor;
use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WebinarReservation;

class AdminDashboardController
{
    public function index()
    {
        $user_count = User::count();
        $professor = Professor::count();
        $student = Student::count();
        $amount = Wallet::sum('balance');
        $private = PrivateClassReservation::latest()->take(4)->get();
        $group = GroupClassReservation::latest()->take(4)->get();
        $webinar = WebinarReservation::latest()->take(4)->get();
        return view('admin.dashboard', compact(['user_count' , 'amount' , 'professor' , 'student' , 'private' , 'group' , 'webinar']));
    }



}
