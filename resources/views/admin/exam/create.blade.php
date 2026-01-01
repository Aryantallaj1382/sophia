@include('admin.upad')

<div class="container py-5" dir="rtl">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">ایجاد آزمون جدید</h3>
        </div>
        <div class="card-body">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <form action="{{ route('admin.exams.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>نوع آزمون</label>
                    <select name="type" class="form-control" id="exam_type">
                        <option value="mock" {{ old('type')=='mock' ? 'selected' : '' }}>آزمون ماک</option>
                        <option value="final" {{ old('type')=='final' ? 'selected' : '' }}>آزمون فاینال</option>
                        <option value="placement" {{ old('type')=='placement' ? 'selected' : '' }}>آزمون تعیین سطح
                        </option>
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
                                <option
                                    value="{{ $group->id }}" {{ old('age_group_id')==$group->id ? 'selected' : '' }}>
                                    {{ $group->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>سطح زبان</label>
                        <select name="language_level_id" class="form-control">
                            @foreach($languageLevels as $level)
                                <option
                                    value="{{ $level->id }}" {{ old('language_level_id')==$level->id ? 'selected' : '' }}>
                                    {{ $level->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>مهارت</label>
                        <select name="skill_id" class="form-control">
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}" {{ old('skill_id')==$skill->id ? 'selected' : '' }}>
                                    {{ $skill->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">نام آزمون</label>
                    <input dir="auto" type="text" name="name" class="form-control"
                           placeholder="مثلاً: آزمون ریاضی پایه" value="{{ old('name') }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">توضیح کوتاه</label>
                    <textarea dir="auto" name="description" class="form-control" rows="2"
                              placeholder="یک توضیح کوتاه درباره آزمون">{{ old('description') }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">زمان (۲۴ ساعته)</label>
                        <input
                            type="time"
                            name="expiration"
                            class="form-control"
                            step="1"
                           >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تعداد دفعات</label>
                        <input dir="auto" type="number" name="number_of_attempts" class="form-control"
                               placeholder="مثلاً: 3" value="{{ old('number_of_attempts') }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تعداد بخش‌ها</label>
                        <input dir="auto" type="number" name="number_of_sections" class="form-control"
                               placeholder="مثلاً: 5" value="{{ old('number_of_sections') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">مدت زمان (دقیقه)</label>
                        <input dir="auto" type="number" name="duration" class="form-control"
                               placeholder="مثلاً: 60" value="{{ old('duration') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">ویو</label>
                    <input dir="auto" type="text" name="view" class="form-control"
                           placeholder="مثلاً: 60" value="{{ old('view') }}">
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                    ثبت آزمون
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
    });
</script>
