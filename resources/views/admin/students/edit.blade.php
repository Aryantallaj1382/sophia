@extends('admin.layouts.app')

@section('title', 'Edit Student')

@section('content')
    <div class="max-w-xl mx-auto bg-white shadow-md p-6 rounded-lg">

        <h2 class="text-xl font-semibold mb-5">Edit Student</h2>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.students.update', $student) }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- First Name -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">First Name</label>
                <input type="text" name="first_name"
                       class="w-full border rounded p-2"
                       value="{{ old('first_name', $student->first_name) }}">
                @error('first_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Last Name -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Last Name</label>
                <input type="text" name="last_name"
                       class="w-full border rounded p-2"
                       value="{{ old('last_name', $student->last_name) }}">
            </div>

            <!-- Nickname -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Nickname</label>
                <input type="text" name="nickname"
                       class="w-full border rounded p-2"
                       value="{{ old('nickname', $student->nickname) }}">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email"
                       class="w-full border rounded p-2"
                       value="{{ old('email', $student->email) }}">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">
                    Password (leave empty to keep current)
                </label>
                <input type="password" name="password"
                       class="w-full border rounded p-2">
            </div>

            <!-- Birth Date -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Birth Date</label>
                <input type="date" name="birth_date"
                       class="w-full border rounded p-2"
                       value="{{ old('birth_date', $student->birth_date) }}">
            </div>

            <!-- WeChat -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">WeChat</label>
                <input type="text" name="we_chat"
                       class="w-full border rounded p-2"
                       value="{{ old('we_chat', $student->we_chat) }}">
            </div>

            <!-- Phone -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" name="phone"
                       class="w-full border rounded p-2"
                       value="{{ old('phone', $student->phone) }}">
            </div>

            <!-- Learning Goals -->
            <div class="mb-6">
                <label class="block mb-2 font-medium">Learning Goals</label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($learningSubgoals as $goal)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox"
                                   name="learning_subgoals[]"
                                   value="{{ $goal->id }}"
                                {{ in_array($goal->id, old('learning_subgoals', $selectedSubgoals)) ? 'checked' : '' }}>
                            {{ $goal->title }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Profile -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Profile Image</label>

                @if($student->user->profile)
                    <img src="{{ asset($student->user->profile) }}"
                         class="w-20 h-20 rounded mb-2">
                @endif

                <input type="file" name="profile"
                       class="w-full border rounded p-2">
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Student
            </button>
        </form>
    </div>
@endsection

