@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-6">Edit Certificate Request</h2>

        <form action="{{ route('admin.certificates.update', $certificate->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="pending" {{ $certificate->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $certificate->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $certificate->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Upload File -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Upload File</label>
                <input type="file" name="file" class="w-full border-gray-300 rounded-md shadow-sm">
                @if($certificate->file)
                    <p class="mt-2 text-sm text-gray-600">
                        Current file:
                        <a href="{{ asset('storage/'.$certificate->file) }}" target="_blank" class="text-indigo-600 hover:underline">Download</a>
                    </p>
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 mt-6">
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Save</button>
                <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Back</a>
            </div>
        </form>
    </div>
@endsection
