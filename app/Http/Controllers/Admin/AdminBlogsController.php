<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class AdminBlogsController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }
    public function create()
    {
        return view('admin.blogs.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
            'reading_time' => 'nullable|integer',
            'views'        => 'nullable|integer',
            'type'         => 'nullable|string|max:100',
            'category'     => 'nullable|string|max:100',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('blogs'), $filename);
            $data['image'] = 'blogs/' . $filename;
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'بلاگ با موفقیت ایجاد شد.');
    }
    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
            'reading_time' => 'nullable|integer',
            'views'        => 'nullable|integer',
            'type'         => 'nullable|string|max:100',
            'category'     => 'nullable|string|max:100',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('blogs'), $filename);
            $data['image'] = 'blogs/' . $filename;
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'بلاگ با موفقیت ویرایش شد.');
    }

    /**
     * حذف بلاگ
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'بلاگ با موفقیت حذف شد.');
    }


}
