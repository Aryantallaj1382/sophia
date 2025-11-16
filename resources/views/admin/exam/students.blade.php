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
                <th>عملیات</th>
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
                    <td>  <a href="{{ route('admin.exams.students.answers', ['exam' => $student->exam_id, 'student' => $student->student_id])}}"
                             class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                            مشاهده نتیجه
                        </a>
                        <button
                            onclick="openScoreModal({{ $student->id }}, '{{ $student->score }}')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                            ویرایش نمره
                        </button>




                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">هیچ دانش‌آموزی در این آزمون شرکت نکرده است.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
<!-- Modal -->
<div id="scoreModal"
     class="fixed inset-0 bg-gray-900/20 hidden flex items-center justify-center backdrop-blur-sm z-50">

    <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl border border-gray-200">

        <h2 class="text-xl font-bold mb-4 text-gray-800">ویرایش نمره دانش‌آموز</h2>

        <form id="scoreForm">
            @csrf
            <input type="hidden" id="examStudentId">

            <label class="block mb-2 text-sm font-medium text-gray-700">نمره</label>
            <input type="number" id="scoreInput"
                   class="w-full border rounded-lg p-2 focus:ring focus:ring-indigo-200"
                   min="0" max="100">

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" onclick="closeScoreModal()"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    لغو
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    ذخیره
                </button>
            </div>
        </form>

    </div>
</div>


<script>
    // باز/بسته کردن مودال
    function openScoreModal(id, score) {
        document.getElementById('examStudentId').value = id;
        document.getElementById('scoreInput').value = score ?? '';
        document.getElementById('scoreModal').classList.remove('hidden');
    }

    function closeScoreModal() {
        document.getElementById('scoreModal').classList.add('hidden');
    }

    // ارسال فرم با AJAX
    document.getElementById('scoreForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let id = document.getElementById('examStudentId').value;
        let score = document.getElementById('scoreInput').value;

        fetch("{{ url('/admin/exam-student') }}/" + id + "/update-score", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ score: score })
        })
            .then(res => res.json())
            .then(data => {
                alert(data.message);       // پیام موفقیت
                closeScoreModal();          // بستن مودال
                location.reload();          // رفرش جدول
            })
            .catch(err => {
                console.error(err);
                alert('خطا در بروزرسانی نمره');
            });
    });
</script>
