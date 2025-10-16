<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use Illuminate\Http\Request;

class AdminBookController
{
    public function index()
    {
        $books = Book::
        latest() // آخرین‌ها اول
        ->paginate(12); // 12 تا در هر صفحه
        return view('admin.book.index', compact('books'));
    }
    public function show($id)
    {
        $book = Book::findOrFail($id);
        return view('admin.book.show', compact('book'));
    }
    public function destroy($id)
    {
        $manhwa = Book::findOrFail($id);
        $manhwa->delete();
        return redirect()->route('admin.books.index');
    }

    public function create()
    {
        return view('admin.book.create');
    }

    // ذخیره کتاب جدید
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'title_file'    => 'nullable|string|max:255',
            'author'        => 'nullable|string|max:255',
            'edition'       => 'nullable|integer|min:1',
            'volume'        => 'nullable|integer|min:1',
            'topics'        => 'nullable|array',
            'book_type'     => 'nullable',

            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video'         => 'nullable|file|mimes:mp4,mov,avi|max:10240',
            'file'          => 'nullable|file|mimes:pdf,doc,docx,epub|max:10240',
            'sample_pages'   => 'nullable|array',
            'sample_pages.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // مسیر پایه public
        $publicPath = public_path('books');

        // اطمینان از وجود پوشه‌ها
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0777, true);
        }
        if (!file_exists($publicPath.'/images')) {
            mkdir($publicPath.'/images', 0777, true);
        }
        if (!file_exists($publicPath.'/videos')) {
            mkdir($publicPath.'/videos', 0777, true);
        }
        if (!file_exists($publicPath.'/files')) {
            mkdir($publicPath.'/files', 0777, true);
        }
        if (!file_exists($publicPath.'/sample_pages')) {
            mkdir($publicPath.'/sample_pages', 0777, true);
        }

        // آپلود تصویر
        if ($request->hasFile('image')) {
            $imageName = uniqid().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('books/images'), $imageName);
            $data['image'] = 'books/images/'.$imageName;
        }

        // آپلود ویدیو
        if ($request->hasFile('video')) {
            $videoName = uniqid().'.'.$request->file('video')->getClientOriginalExtension();
            $request->file('video')->move(public_path('books/videos'), $videoName);
            $data['video'] = 'books/videos/'.$videoName;
        }

        // آپلود فایل کتاب
        if ($request->hasFile('file')) {
            $fileName = uniqid().'.'.$request->file('file')->getClientOriginalExtension();
            $request->file('file')->move(public_path('books/files'), $fileName);
            $data['file'] = 'books/files/'.$fileName;
        }

        // اگر topics به صورت JSON رشته‌ای آمده
        if (!empty($data['topics']) && is_string($data['topics'])) {
            $topics = json_decode($data['topics'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['topics'] = $topics;
            }
        }

        $book = Book::create($data);

        // آپلود صفحات نمونه
        if ($request->hasFile('sample_pages')) {
            foreach ($request->file('sample_pages') as $sample) {
                $sampleName = uniqid().'.'.$sample->getClientOriginalExtension();
                $sample->move(public_path('books/sample_pages'), $sampleName);
                $book->samplePages()->create(['image' => 'books/sample_pages/'.$sampleName]);
            }
        }

        return redirect()->route('admin.books.index')->with('success', '📘 کتاب جدید با موفقیت ایجاد شد.');
    }


    // در صورت نیاز ویرایش
    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'title_file' => 'nullable|string|max:255',
            'author'     => 'nullable|string|max:255',
            'edition'    => 'nullable|integer|min:1',
            'volume'     => 'nullable|integer|min:1',
            'topics'     => 'nullable|array',
            'book_type'     => 'nullable',
            'description'=> 'nullable|string',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video'      => 'nullable|file|mimes:mp4,mov,avi|max:10240',
            'file'       => 'nullable|file|mimes:pdf,doc,docx,epub|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('books/images', 'public');
        }

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('books/videos', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('books/files', 'public');
        }

        if (!empty($data['topics']) && is_string($data['topics'])) {
            $topics = json_decode($data['topics'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['topics'] = $topics;
            }
        }

        $book->update($data);

        return redirect()->route('admin.books.show', $book->id)->with('success', 'کتاب با موفقیت بروزرسانی شد.');
    }
}
