@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Certificate Requests</h2>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">#</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">User</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Title</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">For</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Type</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Status</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">File</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($certificates as $certificate)
                    <tr class="border-t border-gray-200 hover:bg-gray-50">
                        <td class="px-4 text-center py-3">{{ $certificate->id }}</td>
                        <td class="px-4 text-center py-3">{{ $certificate->user->name }}</td>
                        <td class="px-4 text-center py-3">{{ $certificate->title }}</td>
                        <td class="px-4 text-center py-3">{{ $certificate->for }}</td>
                        <td class="px-4 text-center py-3">{{ $certificate->type }}</td>
                        <td class="px-4 text-center py-3">
                            @if($certificate->status == 'approved')
                                <span class="px-2 py-1 text-sm font-semibold text-green-800 bg-green-200 rounded">{{ $certificate->status }}</span>
                            @elseif($certificate->status == 'rejected')
                                <span class="px-2 py-1 text-sm font-semibold text-red-800 bg-red-200 rounded">{{ $certificate->status }}</span>
                            @else
                                <span class="px-2 py-1 text-sm font-semibold text-yellow-800 bg-yellow-200 rounded">{{ $certificate->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 text-center py-3">
                            @if($certificate->file)
                                <a href="{{ asset('storage/'.$certificate->file) }}" target="_blank" class="text-blue-600 hover:underline">Download</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 text-center py-3">
                            <a href="{{ route('admin.certificates.edit', $certificate->id) }}"
                               class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                                Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $certificates->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
