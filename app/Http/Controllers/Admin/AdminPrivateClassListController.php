<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateClassReservation;
use Illuminate\Http\Request;

class AdminPrivateClassListController extends Controller
{
    public function index()
    {
        $reservations = PrivateClassReservation::with([
            'user',
            'professor',
            'timeSlots'
        ])->latest()->get();

        return view('admin.private_classes.index', compact('reservations'));
    }
    public function show($id)
    {
        $reservations = PrivateClassReservation::with([
            'user',
            'professor',
            'ageGroup',
            'languageLevel',
            'platform',
            'subgoal',
            'skill',
            'timeSlots' => function($query) {
                $query->orderBy('date', 'asc')->orderBy('time', 'asc');
            }        ])->findOrFail($id);
        return view('admin.private_classes.show', compact('reservations'));
    }
    public function updateClassLink(Request $request, $id)
    {
        $request->validate([
            'class_link' => 'nullable|url|max:255',
        ]);

        $reservation = PrivateClassReservation::findOrFail($id);
        $reservation->class_link = $request->class_link;
        $reservation->save();

        return redirect()->back()->with('success', 'لینک کلاس با موفقیت بروزرسانی شد.');
    }

}
