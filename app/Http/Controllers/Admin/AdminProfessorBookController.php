<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Professor;
use Illuminate\Http\Request;

class AdminProfessorBookController extends Controller
{
    public function index($id)
    {
        $professor = Professor::with('books')->findOrFail($id);
        $books = Book::all();

        return view('professors.books.edit', compact('professor', 'books'));
    }

    public function update(Request $request, $id)
    {
        $professor = Professor::findOrFail($id);

        $professor->books()->sync($request->input('books', []));

        return redirect()->back()->with('success', 'کتاب‌ها با موفقیت ذخیره شد.');
    }
}
