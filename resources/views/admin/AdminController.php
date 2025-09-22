<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function xadmin()
    {
        return view('admin.adminhome');
    }


    public function tayid_asatid()
    {
        return view('admin.tayid_asatid');
    }

    
}
