@include('admin.upad')

<div class="container py-4" dir="rtl">
    <h4 class="mb-4 text-center">ویرایش سوال: {{ $question->title ?? $question->id }}</h4>

    <form method="POST" action="{{ route('admin.q_update', $question->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">

            <!-- شماره سوال -->
            <div class="col-md-3">
                <input type="number" name="number" class="form-control text-center" placeholder="شماره سوال" required
                       value="{{ old('number', $question->number) }}">
            </div>

            <!-- نوع سوال -->
            <div class="col-md-3">
                <select name="question_type" class="form-select text-center" id="questionTypeSelect" required>
                    <option value="blank" {{ $question->question_type=='blank' ? 'selected' : '' }}>تشریحی</option>
                    <option value="test" {{ $question->question_type=='test' ? 'selected' : '' }}>چندگزینه‌ای</option>
                    <option value="speaking" {{ $question->question_type=='speaking' ? 'selected' : '' }} >speaking</option>
                    <option value="writing" {{ $question->question_type=='writing' ? 'selected' : '' }}>writing</option>
                </select>
            </div>

            <!-- عنوان سوال -->
            <div class="col-md-6">
                <input type="text" name="title" class="form-control text-center" placeholder="عنوان سوال"
                       value="{{ old('title', $question->title) }}">
            </div>

            <!-- توضیح کوتاه و متن سوال -->
            <div class="col-md-12">
                <textarea name="description" class="form-control mb-2" placeholder="توضیح کوتاه">{{ old('description', $question->description) }}</textarea>
                <textarea name="question" class="form-control" placeholder="متن سوال">{{ old('question', $question->question) }}</textarea>
            </div>

            <!-- ورینت‌ها و گزینه‌ها -->
            <div class="col-md-12" id="variantsContainer">
                <div class="card border-info mb-3 p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0 fw-bold">ورینت‌ها و آپشن‌ها (چندگزینه‌ای)</label>
                        <button type="button" id="addVariant" class="btn btn-sm btn-info">افزودن ورینت</button>
                    </div>
                    <div id="variantInputs">
                        @foreach($question->variants as $vIndex => $variant)
                            <div class="variant-block border rounded p-2 mb-2">
                                <input type="text" name="variants[{{ $vIndex }}][question]" class="form-control mb-2"
                                       placeholder="متن ورینت" value="{{ old('variants.'.$vIndex.'.question', $variant->text) }}">

                                <div class="options-container" id="options-{{ $vIndex }}">
                                    @foreach($variant->options as $oIndex => $option)
                                        <div class="input-group mb-2">
                                            <input type="text" name="variants[{{ $vIndex }}][options][{{ $oIndex }}][text]"
                                                   class="form-control" placeholder="گزینه" value="{{ old('variants.'.$vIndex.'.options.'.$oIndex.'.text', $option->text) }}">
                                            <div class="input-group-text">
                                                <input type="checkbox" name="variants[{{ $vIndex }}][options][{{ $oIndex }}][is_correct]" value="1"
                                                    {{ $option->is_correct ? 'checked' : '' }}>
                                                <small class="ms-1">صحیح</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-sm btn-secondary addOption mb-2" data-variant="{{ $vIndex }}">افزودن گزینه</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- فایل‌ها -->
            <div class="col-md-12" id="mediaContainer">
                <div class="card border-secondary mb-3 p-2">
                    <label class="fw-bold">فایل‌ها (اختیاری)</label>
                    <div id="mediaInputs" class="mb-2">
                        @foreach($question->media as $mIndex => $media)
                            <div class="input-group mb-2">
                                <a href="{{ asset($media->path) }}" target="_blank">فایل موجود</a>
                                <input type="text" name="media_description[{{ $mIndex }}]" class="form-control" placeholder="توضیح فایل (اختیاری)" value="{{ $media->description }}">
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="addMedia" class="btn btn-sm btn-secondary">افزودن فایل دیگر</button>
                </div>
            </div>

            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-success px-4">ذخیره تغییرات</button>
            </div>

        </div>
    </form>
</div>

<script>
    const questionTypeSelect = document.getElementById('questionTypeSelect');
    const variantsContainer = document.getElementById('variantsContainer');
    const variantInputs = document.getElementById('variantInputs');
    const addVariantBtn = document.getElementById('addVariant');

    // نمایش یا مخفی کردن ورینت‌ها
    function toggleVariants() {
        variantsContainer.style.display = questionTypeSelect.value === 'test' ? 'block' : 'none';
    }
    toggleVariants();
    questionTypeSelect.addEventListener('change', toggleVariants);

    // افزودن ورینت جدید
    // تابع افزودن گزینه به ورینت
    function attachAddOptionEvent(button) {
        button.addEventListener('click', (e) => {
            const vIndex = e.target.getAttribute('data-variant');
            const optionsContainer = document.getElementById(`options-${vIndex}`);
            const optionCount = optionsContainer.querySelectorAll('.input-group').length;

            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
            <input type="text" name="variants[${vIndex}][options][${optionCount}][text]" class="form-control" placeholder="گزینه ${optionCount + 1}">
            <div class="input-group-text">
                <input type="checkbox" name="variants[${vIndex}][options][${optionCount}][is_correct]" value="1">
                <small class="ms-1">صحیح</small>
            </div>
        `;
            optionsContainer.appendChild(div);
        });
    }

    // ثبت رویداد برای دکمه‌های موجود هنگام لود صفحه
    document.querySelectorAll('.addOption').forEach(btn => {
        attachAddOptionEvent(btn);
    });

    // افزودن ورینت جدید
    addVariantBtn.addEventListener('click', () => {
        const variantIndex = variantInputs.querySelectorAll('.variant-block').length;
        const variantDiv = document.createElement('div');
        variantDiv.className = 'variant-block border rounded p-2 mb-2';
        variantDiv.innerHTML = `
        <input type="text" name="variants[${variantIndex}][question]" class="form-control mb-2" placeholder="متن ورینت">
        <div class="options-container" id="options-${variantIndex}">
            <div class="input-group mb-2">
                <input type="text" name="variants[${variantIndex}][options][0][text]" class="form-control" placeholder="گزینه 1">
                <div class="input-group-text">
                    <input type="checkbox" name="variants[${variantIndex}][options][0][is_correct]" value="1">
                    <small class="ms-1">صحیح</small>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary addOption mb-2" data-variant="${variantIndex}">افزودن گزینه</button>
    `;
        variantInputs.appendChild(variantDiv);

        // ثبت رویداد برای دکمه‌ی تازه
        attachAddOptionEvent(variantDiv.querySelector('.addOption'));
    });

    // افزودن فایل جدید
    document.getElementById('addMedia').addEventListener('click', () => {
        const mediaInputs = document.getElementById('mediaInputs');
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="file" name="media[]" class="form-control">
            <input type="text" name="media_description[]" class="form-control" placeholder="توضیح فایل (اختیاری)">
        `;
        mediaInputs.appendChild(div);
    });
</script>
