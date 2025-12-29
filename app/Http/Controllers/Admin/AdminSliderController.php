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
            'mobile_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tablet_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link'  => 'nullable|url',
        ]);

        // مسیر پوشه‌ای که فایل باید در آن ذخیره شود
        $destinationPath = public_path('sliders');

        // اگر پوشه وجود نداشت، ساخته شود
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move($destinationPath, $fileName);

        $fileName1 = time() . '_' . uniqid() . '.' . $request->file('mobile_image')->getClientOriginalExtension();
        $request->file('mobile_image')->move($destinationPath, $fileName1);


        $fileName2 = time() . '_' . uniqid() . '.' . $request->file('tablet_image')->getClientOriginalExtension();
        $request->file('tablet_image')->move($destinationPath, $fileName2);

        Slider::create([
            'image' => 'sliders/' . $fileName,
            'mobile_image' => 'sliders/' . $fileName1,
            'tablet_image' => 'sliders/' . $fileName1,
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
