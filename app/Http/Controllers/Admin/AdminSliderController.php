<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;

class AdminSliderController
{
    public function index()
    {
        $sliders = Slider::latest()->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    // ذخیره اسلایدر جدید
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link'  => 'nullable|url',
        ]);

        // مسیر پوشه‌ای که فایل باید در آن ذخیره شود
        $destinationPath = public_path('sliders');

        // اگر پوشه وجود نداشت، ساخته شود
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // نام یکتا برای فایل
        $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();

        // انتقال فایل به public/sliders
        $request->file('image')->move($destinationPath, $fileName);

        // ذخیره در دیتابیس
        Slider::create([
            'image' => 'sliders/' . $fileName, // مسیر نسبی برای نمایش
            'link'  => $request->link,
        ]);

        return redirect()->back()->with('success', 'اسلایدر با موفقیت افزوده شد.');
    }


    // حذف اسلایدر
    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        if (file_exists(public_path($slider->image))) {
            unlink(public_path($slider->image));
        }
        $slider->delete();
        return redirect()->back()->with('success', 'اسلایدر حذف شد.');
    }
}
