<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;

class AdminStoryController extends Controller
{
    public function mainPageStories()
    {
        $stories = Story::whereNull('professor_id')
            ->where('main_page', true)
            ->latest()
            ->get();

        return view('admin.stories.index', compact('stories'));
    }
    public function create()
    {
        return view('admin.stories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'video' => 'nullable|mimetypes:video/mp4,video/mpeg,video/quicktime|max:51200', // 50MB
            'main_page' => 'boolean',
        ]);

        $data = [];
        $uploadPath = public_path('uploads/stories');

        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $fileName = time() . '_cover.' . $coverImage->getClientOriginalExtension();
            $coverImage->move($uploadPath, $fileName);
            $data['cover_image'] = 'uploads/stories/' . $fileName;
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $fileName = time() . '_video.' . $video->getClientOriginalExtension();
            $video->move($uploadPath, $fileName);
            $data['video'] = 'uploads/stories/' . $fileName;
        }

        $data['main_page'] = $request->boolean('main_page');
        $data['professor_id'] = null; // چون برای صفحه اصلی هست

        Story::create($data);

        return redirect()->route('admin.stories.main')->with('success', 'استوری با موفقیت اضافه شد ✅');
    }
    public function destroy(Story $story)
    {
        // حذف فایل کاور از public
        if ($story->cover_image && file_exists(public_path($story->cover_image))) {
            unlink(public_path($story->cover_image));
        }

        // حذف فایل ویدیو از public
        if ($story->video && file_exists(public_path($story->video))) {
            unlink(public_path($story->video));
        }

        // حذف رکورد از دیتابیس
        $story->delete();

        return redirect()->route('admin.stories.main')->with('success', '✅ استوری با موفقیت حذف شد.');
    }

}
