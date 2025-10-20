@extends('admin.layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">📚 تمام کتاب ها</h1>

            <a href="{{route('admin.books.create')}}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition transform hover:scale-105">
                ایجاد کتاب جدید
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                <a href="{{ route('admin.books.show', $book->id) }}"
                   class="block bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <img src="{{ asset($book->image) }}" alt="{{ $book->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h2 class="text-lg font-semibold text-gray-800">{{ $book->name }}</h2>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $books->links() }}
        </div>
    </div>
@endsection

