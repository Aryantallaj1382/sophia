@include('admin.upad')


    <div class="container py-4" dir="rtl">
        <h3 class="mb-4">دانش‌آموزان شرکت کرده در آزمون: {{ $exam->name }}</h3>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>نام دانش‌آموز</th>
                <th>ایمیل</th>
                <th>وضعیت</th>
                <th>نمره</th>
            </tr>
            </thead>
            <tbody>
            @forelse($exam->students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->student->name }}</td>
                    <td>{{ $student->student->email }}</td>
                    <td>{{ $student->status_fa}}</td>
                    <td>{{ $student->score ?? 'نمره ندارد'}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">هیچ دانش‌آموزی در این آزمون شرکت نکرده است.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
