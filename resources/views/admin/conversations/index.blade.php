@extends('admin.layouts.app')

@section('content')
    <div class="p-6 min-h-screen bg-slate-100">
        <h1 class="text-2xl font-bold mb-6">📨 تمام چت‌ها</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($conversations as $conversation)
                <a href="{{ route('admin.conversations.show', $conversation->id) }}"
                   class="block p-4 bg-white rounded-lg shadow hover:shadow-lg transition">
                    <h2 class="font-semibold text-lg text-slate-700">
                        {{ $conversation?->name }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-2">
                        {{ $conversation->messages->last()?->message ?? 'پیامی موجود نیست' }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
@endsection
