```html
@include('admin.upad')

<div class="container mt-4">
    <div class="mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link disabled" href="#">مرحله ۱: اطلاعات آزمون</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">مرحله ۲: بخش‌های آزمون</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">مرحله ۳: سوالات</a>
            </li>
        </ul>
    </div>

    <h2 class="mb-4">ایجاد سوال جدید</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.bankexans.questions.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="exam_part_id">انتخاب بخش آزمون</label>
            <select name="exam_part_id" id="exam_part_id" class="form-control" required>
                <option value="">-- انتخاب بخش --</option>
                @foreach($exam_parts as $part)
                    <option value="{{ $part->id }}" {{ old('exam_part_id', $exam_part_id) == $part->id ? 'selected' : '' }}>{{ $part->title }} (آزمون: {{ $part->exam->name }})</option>
                @endforeach
            </select>
        </div>

        <div class="border rounded p-3 mb-4 bg-light">
            <h5>اطلاعات سوال</h5>

            <div class="form-group mb-3">
                <label for="title">عنوان سوال</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                <small class="form-text text-muted">عنوان کوتاه برای سوال وارد کنید.</small>
            </div>

            <div class="form-group mb-3">
                <label for="question_type_id">نوع سوال</label>
                <select name="question_type_id" id="question_type_id" class="form-control" required>
                    <option value="">-- انتخاب نوع --</option>
                    @foreach($question_types as $type)
                        <option value="{{ $type->id }}" {{ old('question_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="question_text">متن سوال</label>
                <textarea name="question_text" id="question_text" class="form-control" rows="4" required>{{ old('question_text') }}</textarea>
                <small class="form-text text-muted">برای جای خالی از @@ استفاده کنید، مثلاً: The sky is usually @@</small>
            </div>

            <div class="form-group mb-3">
                <label for="difficulty">سطح دشواری</label>
                <select name="difficulty" id="difficulty" class="form-control" required>
                    <option value="">-- انتخاب سطح --</option>
                    <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>آسان</option>
                    <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>متوسط</option>
                    <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>سخت</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="multiple_correct">چند پاسخ درست دارد؟</label>
                <select name="multiple_correct" id="multiple_correct" class="form-control" required>
                    <option value="0" {{ old('multiple_correct') == '0' ? 'selected' : '' }}>خیر</option>
                    <option value="1" {{ old('multiple_correct') == '1' ? 'selected' : '' }}>بله</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="short_answer">جواب کوتاه</label>
                <input type="text" name="short_answer" id="short_answer" class="form-control" value="{{ old('short_answer') }}" required pattern="^[a-zA-Z]+ @$" title="فرمت باید مانند 'heart @' یا 'computer @' باشد">
                <small class="form-text text-muted">جواب باید به شکل 'word @' باشد، مثلاً 'heart @'</small>
            </div>
        </div>

        <div class="border rounded p-3 mb-4 bg-light">
            <h5>گزینه‌های واریانت</h5>
            <div id="variants">
                <div class="variant-group mb-3">
                    <label>متن واریانت</label>
                    <textarea name="variants[]" class="form-control" rows="2">{{ old('variants.0') }}</textarea>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addVariant()">افزودن واریانت</button>
        </div>

        <div class="border rounded p-3 mb-4 bg-light">
            <h5>گزینه‌های پاسخ</h5>
            <div id="options">
                <div class="option-group mb-3">
                    <label>متن گزینه</label>
                    <input type="text" name="options[]" class="form-control" value="{{ old('options.0') }}">
                    <label><input type="checkbox" name="is_correct[]" value="1" {{ old('is_correct.0') == '1' ? 'checked' : '' }}> درست است؟</label>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addOption()">افزودن گزینه</button>
        </div>

        <div class="border rounded p-3 mb-4 bg-light">
            <h5>رسانه (اختیاری)</h5>
            <div class="form-group mb-3">
                <label for="media">فایل رسانه</label>
                <input type="file" name="media" id="media" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label for="media_type">نوع رسانه</label>
                <select name="media_type" id="media_type" class="form-control">
                    <option value="">-- انتخاب نوع --</option>
                    <option value="image" {{ old('media_type') == 'image' ? 'selected' : '' }}>تصویر</option>
                    <option value="audio" {{ old('media_type') == 'audio' ? 'selected' : '' }}>صوت</option>
                    <option value="video" {{ old('media_type') == 'video' ? 'selected' : '' }}>ویدئو</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="media_description">توضیحات رسانه</label>
                <textarea name="media_description" id="media_description" class="form-control" rows="2">{{ old('media_description') }}</textarea>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success">ثبت سوال</button>
            <a href="{{ route('admin.bankexans') }}" class="btn btn-secondary">بازگشت به لیست آزمون‌ها</a>
        </div>
    </form>
</div>

<script>
    function addVariant() {
        const variantsDiv = document.getElementById('variants');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-group mb-3';
        newVariant.innerHTML = `
            <label>متن واریانت</label>
            <textarea name="variants[]" class="form-control" rows="2"></textarea>
        `;
        variantsDiv.appendChild(newVariant);
    }

    function addOption() {
        const optionsDiv = document.getElementById('options');
        const newOption = document.createElement('div');
        newOption.className = 'option-group mb-3';
        newOption.innerHTML = `
            <label>متن گزینه</label>
            <input type="text" name="options[]" class="form-control">
            <label><input type="checkbox" name="is_correct[]" value="1"> درست است؟</label>
        `;
        optionsDiv.appendChild(newOption);
    }
</script>
