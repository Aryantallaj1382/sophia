@extends('admin.layouts.app')

@section('content')
    <div class="p-0 min-h-screen flex flex-col bg-gradient-to-b from-slate-900 to-slate-800">

        {{-- هدر --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 bg-slate-900">
            <h1 class="text-lg font-bold text-white">
                📨 گفت‌وگو  <span class="text-indigo-400">{{ $conversation->conversationUsers?->first()?->user?->name }}</span>
            </h1>
            <a href="{{ route('admin.conversations.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition transform hover:scale-105">
                بازگشت
            </a>
            <form action="{{ route('admin.conversations.delete', $conversation->id) }}" method="POST"
                  onsubmit="return confirm('آیا مطمئن هستید؟')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition">
                    حذف
                </button>
            </form>
        </div>

        {{-- پیام‌ها --}}
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-4">
            @foreach ($conversation->messages as $message)
                @php $isUser1 = $message->sender_id === $conversation->conversationUsers->first()->user->id; @endphp
                @if ($isUser1)
                    <div class="flex items-end gap-2">

                        <div class="max-w-md bg-indigo-600 text-white p-3 rounded-xl shadow-md break-words">
                            @if($message->type === 'text')
                                <p class="text-sm leading-relaxed">{{ $message->text }}</p>
                            @elseif($message->type === 'file')
                                <a href="{{ asset($message->file_path) }}" target="_blank" class="underline text-white">
                                    دانلود فایل
                                </a>
                            @elseif($message->type === 'voice')
                                <audio controls class="w-full">
                                    <source src="{{ asset($message->voice_path) }}" type="audio/mpeg">
                                    مرورگر شما پشتیبانی نمی‌کند
                                </audio>
                            @endif
                            <span class="block text-[10px] text-slate-200 opacity-70 mt-1">
                            {{$message->created_at?->format('H:i') }}
                        </span>
                        </div>
                    </div>
                @else
                    <div class="flex items-end gap-2 justify-end">
                        <div class="max-w-md bg-slate-700 text-white p-3 rounded-xl shadow-md break-words">
                            @if($message->type === 'text')
                                <p class="text-sm leading-relaxed">{{ $message->text }}</p>
                            @elseif($message->type === 'file')
                                <a href="{{ asset($message->file_path) }}" target="_blank" class="underline text-white">
                                    دانلود فایل
                                </a>
                            @elseif($message->type === 'voice')
                                <audio controls class="w-full">
                                    <source src="{{ asset($message->voice_path) }}" type="audio/mpeg">
                                    مرورگر شما پشتیبانی نمی‌کند
                                </audio>
                            @endif
                            <span class="block text-[10px] text-slate-300 opacity-70 mt-1 text-right">
                            {{ $message->created_at?->format('H:i') }}
                        </span>
                        </div>
{{--                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm shadow-md">--}}
{{--                            {{ $conversation->conversationUsers?->name[0] }}--}}
{{--                        </div>--}}
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
