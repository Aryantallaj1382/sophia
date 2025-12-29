@include('admin.upad')

<div class="container py-5" dir="rtl">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">ویرایش آزمون</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>نوع آزمون</label>
                    <select name="type" class="form-control" id="exam_type">
                        <option value="mock" {{ $exam->type=='mock' ? 'selected' : '' }}>آزمون ماک</option>
                        <option value="final" {{ $exam->type=='final' ? 'selected' : '' }}>آزمون فاینال</option>
                        <option value="placement" {{ $exam->type=='placement' ? 'selected' : '' }}>آزمون تعیین سطح</option>
                    </select>
                </div>
                <div id="book" style="display:none;">
                    <div class="mb-3">
                        <label>اسم کتاب</label>
                        <select name="books_id" class="form-control">
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ old('book_id')==$book->id ? 'selected' : '' }}>
                                    {{ $book->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="placement_fields" style="display:none;">
                    <div class="mb-3">
                        <label>گروه سنی</label>
                        <select name="age_group_id" class="form-control">
                            @foreach($ageGroups as $group)
                                <option value="{{ $group->id }}" {{ $exam->age_group_id==$group->id ? 'selected' : '' }}>
                                    {{ $group->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>سطح زبان</label>
                        <select name="language_level_id" class="form-control">
                            @foreach($languageLevels as $level)
                                <option value="{{ $level->id }}" {{ $exam->language_level_id==$level->id ? 'selected' : '' }}>
                                    {{ $level->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>مهارت</label>
                        <select name="skill_id" class="form-control">
                            <option value="listening" {{ $exam->skill_id=='listening' ? 'selected' : '' }}>Listening</option>
                            <option value="speaking" {{ $exam->skill_id=='speaking' ? 'selected' : '' }}>Speaking</option>
                            <option value="reading" {{ $exam->skill_id=='reading' ? 'selected' : '' }}>Reading</option>
                            <option value="writing" {{ $exam->skill_id=='writing' ? 'selected' : '' }}>Writing</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">نام آزمون</label>
                    <input dir="auto" type="text" name="name" class="form-control"
                           placeholder="مثلاً: آزمون ریاضی پایه" value="{{ $exam->name }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">توضیح کوتاه</label>
                    <textarea dir="auto" name="description" class="form-control" rows="2"
                              placeholder="یک توضیح کوتاه درباره آزمون">{{ $exam->description }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاریخ انقضا</label>
                        <input dir="auto" type="date" name="expiration" class="form-control"
                               value="{{ $exam->expiration }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تعداد دفعات</label>
                        <input dir="auto" type="number" name="number_of_attempts" class="form-control"
                               placeholder="مثلاً: 3" value="{{ $exam->number_of_attempts }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تعداد بخش‌ها</label>
                        <input dir="auto" type="number" name="number_of_sections" class="form-control"
                               placeholder="مثلاً: 5" value="{{ $exam->number_of_sections }}">
                    </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">زمان (۲۴ ساعته)</label>
                            <input
                                type="time"
                                name="duration"
                                class="form-control"
                                step="1"
                                value="{{ $exam->duration ? \Carbon\Carbon::parse($exam->duration)->format('H:i:s') : '' }}">

                        </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">ویو</label>
                    <input dir="auto" type="text" name="view" class="form-control"
                           placeholder="مثلاً: 60" value="{{ $exam->view }}">
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                    بروزرسانی آزمون
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    body {
        font-family: 'Vazir', sans-serif;
    }
    .form-label {
        font-size: 0.95rem;
    }
    .card {
        border-radius: 12px;
    }
    .btn-success {
        background: linear-gradient(90deg, #28a745, #198754);
        border: none;
    }
</style>

<script>
    const examType = document.getElementById('exam_type');
    const placementFields = document.getElementById('placement_fields');
    const book = document.getElementById('book');

    examType.addEventListener('change', function () {
        placementFields.style.display = this.value === 'placement' ? 'block' : 'none';
        book.style.display = this.value === 'final' ? 'block' : 'none';
    });


    window.addEventListener('load', function () {
        placementFields.style.display = examType.value === 'placement' ? 'block' : 'none';
        book.style.display = examType.value === 'final' ? 'block' : 'none';
    });
</script>
