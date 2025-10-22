@extends('admin.layouts.app')

@section('content')
    <div class="p-0 min-h-screen flex flex-col bg-gradient-to-b from-gray-100 to-gray-50">

        {{-- هدر --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white shadow-sm">
            <h1 class="text-lg font-bold text-gray-800">
                📨 گفت‌وگو با <span class="text-indigo-600">{{ $ticket->user->name }}</span>
            </h1>
            <span class="text-sm text-gray-500">
                وضعیت:
                 @if ($ticket->status === 'open')
                    <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-lg">باز</span>
                @elseif($ticket->status === 'pending')
                    <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-200 rounded-lg">در انتظار پاسخ</span>
                @elseif($ticket->status === 'answered')
                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-lg">پاسخ داده شده</span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-lg">بسته</span>
                @endif
            </span>
            <a href="{{ route('admin.tickets.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition transform hover:scale-105">
                بازگشت
            </a>
        </div>

        {{-- پیام‌ها (اسکرول) --}}
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-4 bg-white">
            @foreach ($ticket->messages as $message)
                @if ($message->is_support_reply == true)
                    <div class="flex items-end gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm shadow-md">A</div>
                        <div class="max-w-md bg-indigo-100 text-gray-800 p-3 rounded-xl shadow-md break-words">
                            <p class="text-sm leading-relaxed">{{ $message->message }}</p>
                            <span class="block text-[10px] text-gray-500 opacity-70 mt-1">
                                {{ $message->created_at?->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="flex items-end gap-2 justify-end">
                        <div class="max-w-md bg-gray-200 text-gray-800 p-3 rounded-xl shadow-md break-words">
                            <p class="text-sm leading-relaxed">{{ $message->message }}</p>
                            <span class="block text-[10px] text-gray-500 opacity-70 mt-1 text-right">
                                {{ $message->created_at?->format('H:i') }}
                            </span>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm shadow-md">U</div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- فرم ارسال پیام (ثابت پایین صفحه) --}}
        <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST"
              class="sticky bottom-0 flex items-center gap-2 px-4 py-3 border-t border-gray-200 bg-white shadow-sm">
            @csrf
            <textarea name="message"
                      class="flex-1 p-3 rounded-lg bg-gray-50 text-gray-800 placeholder-gray-400 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                      rows="1"
                      placeholder="پیام خود را بنویسید..."></textarea>
            <button type="submit"
                    class="px-5 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg shadow-lg transition transform hover:scale-105">
                ارسال
            </button>
        </form>
    </div>
@endsection
