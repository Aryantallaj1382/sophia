@include('admin.upad')

<div class="container py-4" dir="rtl">
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


    <h4 class="mb-4 text-center">مدیریت سوالات بخش: {{ $part->title ?? $part->id }}</h4>
    <small class="text-muted d-block text-center mb-3">برای جای خالی از <b>@@</b> استفاده کنید </small>
    <small class="text-muted d-block text-center mb-3">برای برجسته شدن متن از <b>$متن$</b> استفاده کنید</small>


    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
        افزودن سوال جدید
    </button>

    <button type="button" class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#questionBankModal">
        انتخاب از بانک سوالات
    </button>
    <a href="{{ route('admin.exams.show', $part->exam_id) }}" class="btn btn-secondary mb-3">
        ← بازگشت
    </a>
    <!-- مودال انتخاب سوال -->
    <div class="modal fade" id="questionBankModal" tabindex="-1" aria-labelledby="questionBankModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="questionBankModalLabel">بانک سوالات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">

                    <!-- فرم سرچ و فیلتر -->
                    <form method="GET" action="{{ route('admin.exam_questions.index', $part->id) }}"
                          class="row g-2 mb-3">
                        <input type="hidden" name="modal" value="questionBank">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="جستجو در عنوان..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="type" class="form-select">
                                <option value="">همه نوع‌ها</option>
                                <option value="blank" {{ request('type') == 'blank' ? 'selected' : '' }}>تشریحی</option>
                                <option value="test" {{ request('type') == 'test' ? 'selected' : '' }}>چندگزینه‌ای
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">اعمال</button>
                        </div>
                    </form>


                    <!-- جدول -->
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>نوع سوال</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($questionsBank as $q)
                            <tr>
                                <td>{{ $q->id }}</td>
                                <td>{{ $q->title ?? '-' }}</td>
                                <td>
        <span class="badge bg-{{ $q->question_type == 'test' ? 'info' : 'secondary' }}">
            {{ $q->question_type }}
        </span>
                                </td>
                                <td>{{ $q->question ?? '-' }}</td>

                                <!-- ستون ورینت‌ها و گزینه‌ها با Popover -->
                                <td>
                                    @if($q->question_type == 'test' && $q->variants)
                                        <span
                                            tabindex="0"
                                            class="text-primary fw-bold"
                                            data-bs-toggle="popover"
                                            data-bs-html="true"
                                            data-bs-content="
                    <ul style='padding-left:15px; margin:0;'>
                        @foreach($q->variants as $variant)
                            <li>
                                <strong>ورینت:</strong> {{ $variant->text }}
                                <ul style='padding-left:15px; margin:0;'>
                                    @foreach($variant->options as $index => $opt)
                                        <li>{{ $index + 1 }}: {{ $opt->text }} @if($opt->is_correct)<span style='color:green;'>✔</span>@endif</li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                "
                                        >•••</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <form action="{{ route('admin.exam_questions.clone', [$part->id, $q->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">انتخاب</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    <!-- پیجینیشن -->
                    <div class="d-flex justify-content-center">
                        {{ $questionsBank->appends(request()->except('page') + ['modal' => 'questionBank'])->links() }}
                    </div>


                </div>
            </div>
        </div>
    </div>
    @if(request('modal') == 'questionBank')
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('questionBankModal'));
            myModal.show();
        </script>
    @endif

    <!-- فرم افزودن سوال -->
    <!-- Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addQuestionModalLabel">افزودن سوال جدید</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.exam_questions.store', $part->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">



                            <div class="col-md-3">
                                <select name="question_type" class="form-select text-center" id="questionTypeSelect"
                                        required>
                                    <option value="blank">تشریحی</option>
                                    <option value="test">چندگزینه‌ای</option>
                                    <option value="speaking">speaking</option>
                                    <option value="writing">writing</option>
                                </select>
                            </div>
                            <div class="col-md-9">
                                <input dir="auto" type="text" name="title" class="form-control text-center"
                                       placeholder="عنوان سوال">
                            </div>

                            <!-- توضیح کوتاه و متن سوال -->
                            <div class="col-md-12">
                                <textarea dir="auto" name="description" class="form-control mb-2"
                                          placeholder="توضیح کوتاه"></textarea>
                                <textarea dir="auto" name="question" id="blank_question" class="form-control"
                                          placeholder="متن سوال"></textarea>
                            </div>

                            <!-- ورینت‌ها و آپشن‌ها -->
                            <div class="col-md-12" id="variantsContainer">
                                <div class="card border-info mb-3 p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="mb-0 fw-bold">ورینت‌ها و آپشن‌ها (چندگزینه‌ای)</label>
                                        <button type="button" id="addVariant" class="btn btn-sm btn-info">افزودن ورینت
                                        </button>
                                    </div>
                                    <div id="variantInputs"></div>
                                </div>
                            </div>

                            <!-- مدیا -->
                            <div class="col-md-12" id="mediaContainer">
                                <div class="card border-secondary mb-3 p-2">
                                    <label class="fw-bold">فایل‌ها (اختیاری)</label>
                                    <div id="mediaInputs" class="mb-2">
                                        <div dir="auto" class="input-group mb-2">
                                            <input dir="auto" type="file" name="media[]" class="form-control">
                                            <input dir="auto" type="text" name="media_description[]"
                                                   class="form-control" placeholder="توضیح فایل (اختیاری)">
                                        </div>
                                    </div>
                                    <button type="button" id="addMedia" class="btn btn-sm btn-secondary">افزودن فایل
                                        دیگر
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success px-4">ثبت سوال</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

        <div class="row g-3">
            @forelse($questions as $question)
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>#{{ $question->number }}</strong> - {{ $question->title ?? '-' }}
                                <span class="badge bg-{{ $question->question_type == 'test' ? 'info' : 'secondary' }}">
                                {{ $question->question_type  }}
                            </span>
                            </div>
                            <div>
                                <a href="{{ route('admin.q_edit', [ $question->id]) }}" class="btn btn-sm btn-warning">ویرایش</a>
                                <form action="{{ route('admin.exam_questions.destroy', [$part->id, $question->id]) }}"
                                      method="POST" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">

                            <p><strong>توضیح کوتاه:</strong> {{ $question->description ?? '-' }}</p>
                            <p><strong>متن سوال:</strong> {{ $question->question ?? '-' }}</p>

                            <!-- ورینت‌ها -->
                            @if($question->question_type == 'test' && $question->variants)
                                <div class="mb-2">
                                    @foreach($question->variants as $variant)
                                        <div class="border rounded p-2 mb-1">
                                            <strong>تیتر سوال:</strong> {{ $variant->text }}
                                            <br>
                                            <span>گزینه ها : </span>
                                            <ul class="mb-0">
                                                @foreach($variant->options as $index =>  $opt)
                                                    <li>{{ $index + 1 ." : ". $opt->text }}</li>
                                                @endforeach
                                            </ul>

                                        </div>

                                    @endforeach
                                </div>
                            @endif

                            <!-- مدیا -->
                            @if($question->media->isNotEmpty())
                                <div>
                                    @foreach($question->media as $media)
                                        <div class="mb-1">
                                            <a href="{{ asset($media->path) }}" target="_blank">فایل</a>
                                            @if($media->description)
                                                <small class="text-muted d-block">{{ $media->description }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center my-3">هیچ سوالی در این بخش موجود نیست.</p>
            @endforelse
        </div>
    </div>

    <script>
        const questionTypeSelect = document.getElementById('questionTypeSelect');
        const variantsContainer = document.getElementById('variantsContainer');
        const blank_question = document.getElementById('blank_question');
        const variantInputs = document.getElementById('variantInputs');
        const addVariantBtn = document.getElementById('addVariant');

        // مدیریت نمایش ورینت‌ها
        function toggleVariants() {
            if (questionTypeSelect.value === 'test') {
                variantsContainer.style.display = 'block';
                blank_question.style.display = 'none';
            } else {
                blank_question.style.display = 'block';
                variantsContainer.style.display = 'none';
                variantInputs.innerHTML = '';
            }
        }

        questionTypeSelect.addEventListener('change', toggleVariants);
        toggleVariants();

        // افزودن ورینت و آپشن‌ها
        addVariantBtn.addEventListener('click', () => {
            const variantIndex = variantInputs.querySelectorAll('.variant-block').length;
            const variantDiv = document.createElement('div');
            variantDiv.className = 'variant-block border rounded p-2 my-2';
            variantDiv.innerHTML = `
    <input dir="auto" type="text" name="variants[${variantIndex}][question]" class="form-control mb-2" placeholder="متن ورینت">
    <div class="options-container" id="options-${variantIndex}">
        <div class="input-group mb-2">
            <input dir="auto" type="text" name="variants[${variantIndex}][options][0][text]" class="form-control" placeholder="گزینه 1">
            <div class="input-group-text">
                <input  type="checkbox" name="variants[${variantIndex}][options][0][is_correct]" value="1">
                <small class="ms-1">صحیح</small>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-secondary addOption mb-2" data-variant="${variantIndex}">افزودن گزینه</button>
`;


            variantInputs.appendChild(variantDiv);

            variantDiv.querySelector('.addOption').addEventListener('click', (e) => {
                const vIndex = e.target.getAttribute('data-variant');
                const optionsContainer = document.getElementById(`options-${vIndex}`);
                const optionCount = optionsContainer.querySelectorAll('.input-group').length;

                const div = document.createElement('div');
                div.className = 'input-group mb-2';
                div.innerHTML = `
        <input  dir="auto" type="text" name="variants[${vIndex}][options][${optionCount}][text]" class="form-control" placeholder="گزینه ${optionCount + 1}">
        <div class="input-group-text">
            <input type="checkbox" name="variants[${vIndex}][options][${optionCount}][is_correct]" value="1">
            <small class="ms-1">صحیح</small>
        </div>
    `;
                optionsContainer.appendChild(div);
            });
        });

        // افزودن فایل و توضیح مدیا
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                var popover = new bootstrap.Popover(popoverTriggerEl);

                // بستن سایر popover ها وقتی این یکی باز میشه
                popoverTriggerEl.addEventListener('show.bs.popover', function () {
                    popoverList.forEach(function (p) {
                        if (p !== popover) {
                            p.hide();
                        }
                    });
                });
                return popover;
            });
        });

    </script>
