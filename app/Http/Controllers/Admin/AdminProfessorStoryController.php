<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProfessorStoryController extends Controller
{
    public function index(Professor $professor)
    {
        // فقط استوری‌های این استاد
        $stories = $professor->story()->latest()->paginate(10);

        return view('stories.index', compact('professor', 'stories'));
    }

    public function store(Request $request, Professor $professor)
    {
        $request->validate([
            'cover_image' => 'required|image|mimes:jpg,jpeg,png',
            'video' => 'required|mimes:mp4,mov,avi|max:20000',
        ]);

        $coverPath = $request->file('cover_image')->store('stories/covers', 'public');
        $videoPath = $request->file('video')->store('stories/videos', 'public');

        $professor->story()->create([
            'cover_image' => $coverPath,
            'video' => $videoPath,
        ]);

        return redirect()->route('admin.professorsStory.index', $professor)->with('success', 'استوری با موفقیت اضافه شد.');
    }

    public function destroy(Professor $professor, Story $story)
    {
        // مطمئن شو این استوری به این استاد تعلق دارد
        if ($story->professor_id !== $professor->id) {
            abort(403);
        }

        if ($story->cover_image) {
            Storage::disk('public')->delete($story->cover_image);
        }

        if ($story->video) {
            Storage::disk('public')->delete($story->video);
        }

        $story->delete();

        return back()->with('success', 'استوری حذف شد.');
    }
}
