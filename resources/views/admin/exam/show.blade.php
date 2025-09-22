@include('admin.upad')

<div class="container py-5" dir="rtl">
    <h1 class="mb-5 text-center display-6">{{ $exam->name }} - مدیریت بخش‌ها</h1>
    <span class="mb-5 text-center w-full block">برای بخش writing و speaking فقط یک سوال ایجاد کنید که تنها شماره ی سوال رو 1 بزنید و فیلد دیگری پر نکنید</span>

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

    <!-- دکمه افزودن بخش جدید -->
    <div class="mb-4 text-center">
        <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#addPartModal">
            افزودن بخش جدید
        </button>
    </div>

    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary mb-3">← بازگشت</a>

    {{-- Modal افزودن بخش --}}
    <div class="modal fade" id="addPartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">افزودن بخش جدید</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.exams.parts.store', $exam->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">نوع بخش:</label>
                                <select name="exam_part_type_id" class="form-select" id="examPartTypeSelect" required>
                                    @foreach($partTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">شماره بخش:</label>
                                <input dir="auto" type="number" name="number" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">عنوان بخش:</label>
                                <input dir="auto" type="text" name="title" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">عنوان سوالات:</label>
                                <input dir="auto" type="text" name="question_title" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">متن اصلی:</label>
                                <textarea dir="auto" name="text" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 passagee" style="display:none;">
                                <label class="form-label fw-bold">عنوان Passage:</label>
                                <input dir="auto" type="text" name="passenger_title" class="form-control">
                            </div>
                            <div class="col-md-12 passagee" style="display:none;">
                                <label class="form-label fw-bold">Passage:</label>
                                <textarea dir="auto" name="passenger" class="form-control" rows="3"></textarea>
                            </div>

                            <!-- دکمه باز کردن مدال بانک مدیا -->
                            <button type="button" class="btn btn-sm btn-info mb-2" id="openMediaBank">
                                انتخاب از بانک مدیا
                            </button>

                            <!-- فایل‌ها -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">فایل‌ها (اختیاری)</label>
                                <div id="mediaInputs">
                                    <div class="input-group mb-2">
                                        <input dir="auto" type="file" name="media[]" class="form-control">
                                        <input dir="auto" type="text" name="media_description[]" class="form-control" placeholder="توضیح فایل (اختیاری)">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-info mt-2" id="addMedia">افزودن فایل دیگر</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-3 w-100 fw-bold">اضافه کردن بخش</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal بانک مدیا --}}
    <div class="modal fade" id="mediaBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">بانک مدیا</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th> فایل</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($mediaBank as $media)
                            <tr>
                                <td>{{ $media->id }}</td>
                                <td>
                                    @php
                                        $ext = pathinfo($media->path, PATHINFO_EXTENSION);
                                    @endphp


                                    @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                                        <img src="{{$media->path}}" alt="media" style="max-height:60px;">
                                    @elseif(in_array(strtolower($ext), ['mp4','webm','ogg']))
                                        <video width="120" controls>
                                            <source src="{{$media->path}}" type="video/{{ $ext }}">
                                            مرورگر شما ویدئو را پشتیبانی نمی‌کند.
                                        </video>
                                    @elseif(in_array($ext, ['mp3','wav','ogg']))
                                        <audio controls>
                                            <source src="{{$media->path}}" type="audio/{{ $ext }}">
                                            مرورگر شما صوت را پشتیبانی نمی‌کند.
                                        </audio>
                                    @else
                                        <a href="{{  $media->path }}" target="_blank">{{ basename($media->path) }}</a>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success selectMediaBtn"
                                            data-path="{{ $media->path }}"
                                            data-description="{{ $media->description ?? '' }}">
                                        انتخاب
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-5">
        <h3 class="mb-3">بخش‌های موجود</h3>
        @if($exam->parts->count())
            <div class="row g-3">
                @foreach($exam->parts as $part)
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title mb-2">{{ $part->type->name }} - {{ $part->title ?? 'بدون عنوان' }}</h5>
                                    <p class="card-text text-muted mb-2">شماره بخش: <span class="badge bg-primary">{{ $part->number }}</span></p>
                                    @if($part->media->isNotEmpty())
                                        @foreach($part->media as $media)
                                            <div>
                                                <a href="{{ asset('storage/' . $media->path) }}" target="_blank">مشاهده فایل</a>
                                                <small class="text-muted d-block">{{ $media->description }}</small>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="mt-3 d-flex justify-content-between">
                                    <a href="{{ route('admin.exam_questions.index', $part->id) }}" class="btn btn-sm btn-outline-info">مدیریت سوالات</a>

                                    <!-- ویرایش و حذف -->
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editPartModal{{ $part->id }}">
                                        ویرایش
                                    </button>
                                    <form action="{{ route('admin.exams.parts.destroy', [$exam->id, $part->id]) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">حذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal ویرایش بخش -->
                    <div class="modal fade" id="editPartModal{{ $part->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title">ویرایش بخش</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.exams.parts.update', [$exam->id, $part->id]) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">نوع بخش:</label>
                                                <select name="exam_part_type_id" class="form-select" required>
                                                    @foreach($partTypes as $type)
                                                        <option value="{{ $type->id }}" {{ $part->exam_part_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">شماره بخش:</label>
                                                <input dir="auto" type="number" name="number" class="form-control" required value="{{ $part->number }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">عنوان بخش:</label>
                                                <input dir="auto" type="text" name="title" class="form-control" value="{{ $part->title }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">عنوان سوالات:</label>
                                                <input dir="auto" type="text" name="question_title" class="form-control" value="{{ $part->question_title }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold">متن اصلی:</label>
                                                <textarea dir="auto" name="text" class="form-control" rows="3">{{ $part->text }}</textarea>
                                            </div>

                                            <div class="col-md-6 passagee">
                                                <label class="form-label fw-bold">عنوان Passage:</label>
                                                <input dir="auto" type="text" name="passenger_title" class="form-control" value="{{ $part->passenger_title }}">
                                            </div>
                                            <div class="col-md-12 passagee">
                                                <label class="form-label fw-bold">Passage:</label>
                                                <textarea dir="auto" name="passenger" class="form-control" rows="3">{{ $part->passenger }}</textarea>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label fw-bold">فایل‌ها (اختیاری)</label>
                                                <div id="mediaInputsEdit{{ $part->id }}">
                                                    @if($part->media->count())
                                                        @foreach($part->media as $media)
                                                            <div class="input-group mb-2">
                                                                <a href="{{ asset('storage/' . $media->path) }}" target="_blank" class="form-control">{{ $media->path }}</a>
                                                                <input dir="auto" type="text" name="media_description[]" class="form-control" value="{{ $media->description }}" placeholder="توضیح فایل (اختیاری)">
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="input-group mb-2">
                                                            <input type="file" name="media[]" class="form-control">
                                                            <input type="text" name="media_description[]" class="form-control" placeholder="توضیح فایل (اختیاری)">
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-info mt-2 addMediaEdit" data-container="mediaInputsEdit{{ $part->id }}">
                                                    افزودن فایل دیگر
                                                </button>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-warning mt-3 w-100 fw-bold">ذخیره تغییرات</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
        @else

            <p class="text-muted fst-italic">هیچ بخشی هنوز اضافه نشده است.</p>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const select = document.getElementById('examPartTypeSelect');
        const passageDivs = document.querySelectorAll('.passagee');

        function togglePassage() {
            const selectedText = select.options[select.selectedIndex].text.toLowerCase();
            passageDivs.forEach(div => {
                div.style.display = selectedText === 'reading' ? 'block' : 'none';
            });
        }
        select.addEventListener('change', togglePassage);

        // افزودن فایل داینامیک
        document.getElementById('addMedia').addEventListener('click', () => {
            const mediaInputs = document.getElementById('mediaInputs');
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
            <input dir="auto" type="file" name="media[]" class="form-control">
            <input dir="auto" type="text" name="media_description[]" class="form-control" placeholder="توضیح فایل (اختیاری)">
        `;
            mediaInputs.appendChild(div);
        });

        // باز کردن مدال بانک مدیا بدون بستن مدال افزودن بخش
        const mediaBankBtn = document.getElementById('openMediaBank');
        mediaBankBtn.addEventListener('click', () => {
            const mediaModalEl = document.getElementById('mediaBankModal');
            const mediaModal = new bootstrap.Modal(mediaModalEl, {
                backdrop: true,
                keyboard: false
            });
            mediaModal.show();
        });

        // انتخاب مدیا و افزودن به فرم بدون بستن مدال اصلی
        document.querySelectorAll('.selectMediaBtn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const path = this.dataset.path;
                const description = this.dataset.description;

                const mediaInputs = document.getElementById('mediaInputs');
                const div = document.createElement('div');
                div.className = 'input-group mb-2';
                div.innerHTML = `
                <input type="hidden" name="selected_media[]" value="${path}">
                <input type="text" class="form-control" value="${path}" readonly>
                <input type="text" name="selected_media_description[]" class="form-control" value="${description}">
                <button type="button" class="btn btn-danger removeMedia">حذف</button>
            `;
                mediaInputs.appendChild(div);

                div.querySelector('.removeMedia').addEventListener('click', () => div.remove());

                // فقط مدال بانک مدیا را ببند
                const mediaModalEl = document.getElementById('mediaBankModal');
                const mediaModal = bootstrap.Modal.getInstance(mediaModalEl);
                mediaModal.hide();
            });
        });
    });
</script>

