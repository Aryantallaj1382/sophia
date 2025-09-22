@include('admin.upad')

<div class="container py-4" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-primary">📑 مدیریت آزمون‌ها</h3>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            ➕ آزمون جدید
        </a>
    </div>

    <div class="table-responsive shadow-sm rounded-3">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="bg-primary text-white">
            <tr>
                <th>نام</th>
                <th>نوع</th>
                <th>توضیح</th>
                <th>تاریخ انقضا</th>
                <th>دفعات مجاز</th>
                <th>تعداد بخش‌ها</th>
                <th>مدت زمان</th>
                <th>ویو</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($exams as $exam)
                <tr>
                    <td class="fw-bold">{{ $exam->name }}</td>
                    <td><span class="badge bg-danger-subtle text-green-700 fw-semibold">{{ $exam->type }}</span></td>

                    <td class="text-muted"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="{{ $exam->description }}">
                        {{ \Illuminate\Support\Str::words($exam->description, 5, '...') }}
                    </td>
                    <td><span class="badge bg-danger-subtle text-danger fw-semibold">{{ $exam->expiration }}</span></td>
                    <td><span class="badge bg-info-subtle text-info">{{ $exam->number_of_attempts }}</span></td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $exam->number_of_sections }}</span></td>
                    <td><span class="badge bg-warning-subtle text-dark">{{ $exam?->duration?->format('H:i:s') }} دقیقه</span></td>
                    <td><span class="badge bg-success-subtle text-success">{{ $exam->view }}</span></td>
                    <td>
                        <div class="btn-group flex items-center gap-2">
                            <a href="{{ route('admin.exams.show', $exam->id) }}"
                               class="btn btn-sm btn-outline-info">👁 مشاهده</a>
                            <a href="{{ route('admin.exams.students', $exam->id) }}"
                               class="btn btn-sm btn-outline-primary">👁 لیست دانش آموزان</a>
                            <a href="{{ route('admin.exams.edit', $exam->id) }}"
                               class="btn btn-sm btn-outline-secondary">✏️ ویرایش</a>
                            <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST"
                                  onsubmit="return confirm('آیا از حذف این آزمون مطمئن هستید؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">🗑 حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        🚫 هیچ آزمونی یافت نشد.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $exams->links('pagination::bootstrap-5') }}
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
