@extends('admin.layouts.app')

@section('title', 'Register Student')

@section('content')
    <div class="max-w-xl mx-auto bg-white shadow-md p-6 rounded-lg">

        <h2 class="text-xl font-semibold mb-5">Register New Student</h2>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.students.register') }}" method="POST" enctype="multipart/form-data">
            @csrf


            <!-- First Name -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">First Name</label>
                <input type="text" name="first_name"
                       class="w-full border rounded p-2"
                       value="{{ old('first_name') }}">
                @error('first_name')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Last Name</label>
                <input type="text" name="last_name"
                       class="w-full border rounded p-2"
                       value="{{ old('last_name') }}">
                @error('last_name')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">nickname</label>
                <input type="text" name="nickname"
                       class="w-full border rounded p-2"
                       value="{{ old('nickname') }}">
                @error('nickname')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email"
                       class="w-full border rounded p-2"
                       value="{{ old('email') }}">
                @error('email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded p-2">
                @error('password')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Birth Date -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Birth Date</label>
                <input type="date" name="birth_date"
                       class="w-full border rounded p-2"
                       value="{{ old('birth_date') }}">
                @error('birth_date')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">we chat</label>
                <input type="text" name="we_chat"
                       class="w-full border rounded p-2"
                       value="{{ old('we_chat') }}">
                @error('we_chat')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">phone</label>
                <input type="text" name="phone"
                       class="w-full border rounded p-2"
                       value="{{ old('phone') }}">
                @error('phone')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <!-- Learning Goals -->
            <div class="mb-6">
                <label class="block mb-2 font-medium">
                    Learning Goals
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($learningSubgoals as $goal)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox"
                                   name="learning_subgoals[]"
                                   value="{{ $goal->id }}"
                                   class="rounded border-gray-300"
                                {{ in_array($goal->id, old('learning_subgoals', [])) ? 'checked' : '' }}>
                            {{ $goal->title }}
                        </label>
                    @endforeach
                </div>

                @error('learning_subgoals')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Profile Image -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Profile Image</label>
                <input type="file" name="profile"
                       class="w-full border rounded p-2"
                       accept="image/*">
                @error('profile')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Register Student
            </button>

        </form>
    </div>
@endsection
