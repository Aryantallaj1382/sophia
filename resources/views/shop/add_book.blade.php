<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="viewport" content="width=device-width, initial-scale=1.0
        name="UTF-8">
    <title>افزودن و مدیریت کتاب‌ها</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .bg-gradient-custom { background: linear-gradient(to right, #f7fafc, #edf2f7); }
        .form-input, .form-select { border-radius: 0.375rem; }
        .table th, .table td { vertical-align: middle; }
        .preview-img { max-height: 150px; object-fit: cover; }
        .form-label { font-weight: bold; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
@include('admin.upad')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="p-6 max-w-7xl mx-auto">
    <h2 class="text-xl font-bold mb-6">📚 افزودن کتاب جدید</h2>

    <!-- فرم افزودن کتاب -->
    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4 bg-white shadow p-4 rounded">
        @csrf

        <!-- عنوان کتاب -->
        <div>
            <label for="title" class="form-label">عنوان کتاب</label>
            <input type="text" id="title" name="title" placeholder="عنوان کتاب" class="form-input border p-2 w-full" value="{{ old('title') }}" required>
            @error('title') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- نویسنده -->
        <div>
            <label for="author" class="form-label">نویسنده</label>
            <input type="text" id="author" name="author" placeholder="نویسنده" class="form-input border p-2 w-full" value="{{ old('author') }}">
            @error('author') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- قیمت -->
        <div>
            <label for="price" class="form-label">قیمت</label>
            <input type="number" id="price" name="price" placeholder="قیمت" class="form-input border p-2 w-full" value="{{ old('price') }}" required>
            @error('price') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- قیمت با تخفیف -->
        <div>
            <label for="off_price" class="form-label">قیمت با تخفیف</label>
            <input type="number" id="off_price" name="off_price" placeholder="قیمت با تخفیف" class="form-input border p-2 w-full" value="{{ old('off_price') }}">
            @error('off_price') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- موجودی -->
        <div>
            <label for="quantity" class="form-label">موجودی</label>
            <input type="number" id="quantity" name="quantity" placeholder="موجودی" class="form-input border p-2 w-full" value="{{ old('quantity') }}" required>
            @error('quantity') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>


        <!-- تعداد جلد -->
        <div>
            <label for="Volume_number" class="form-label">تعداد جلد</label>
            <input type="number" id="Volume_number" name="Volume_number" placeholder="تعداد جلد" class="form-input border p-2 w-full" value="{{ old('Volume_number') }}">
            @error('Volume_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- تعداد صفحات -->
        <div>
            <label for="page_number" class="form-label">تعداد صفحات</label>
            <input type="number" id="page_number" name="page_number" placeholder="تعداد صفحات" class="form-input border p-2 w-full" value="{{ old('page_number') }}">
            @error('page_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- سال انتشار -->
        <div>
            <label for="year_of_publication" class="form-label">سال انتشار</label>
            <input type="number" id="year_of_publication" name="year_of_publication" placeholder="سال انتشار" class="form-input border p-2 w-full" value="{{ old('year_of_publication') }}" min="1000">
            @error('year_of_publication') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- ابعاد -->
        <div>
            <label for="Dimensions" class="form-label">ابعاد</label>
            <input type="text" id="Dimensions" name="Dimensions" placeholder="ابعاد (مثال: 25x15x5)" class="form-input border p-2 w-full" value="{{ old('Dimensions') }}">
            @error('Dimensions') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- زمان چاپ -->
        <div>
            <label for="Time_to_print" class="form-label">زمان چاپ (روز)</label>
            <input type="number" id="Time_to_print" name="Time_to_print" placeholder="زمان چاپ (روز)" class="form-input border p-2 w-full" value="{{ old('Time_to_print') }}">
            @error('Time_to_print') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- نوع کاغذ -->
        <div>
            <label for="Paper_type" class="form-label">نوع کاغذ</label>
            <input type="text" id="Paper_type" name="Paper_type" placeholder="نوع کاغذ" class="form-input border p-2 w-full" value="{{ old('Paper_type') }}">
            @error('Paper_type') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- نوع پوشش -->
        <div>
            <label for="Cover_type" class="form-label">نوع پوشش</label>
            <input type="text" id="Cover_type" name="Cover_type" placeholder="نوع پوشش" class="form-input border p-2 w-full" value="{{ old('Cover_type') }}">
            @error('Cover_type') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- شماره شابک -->
        <div>
            <label for="shabak_number" class="form-label">شماره شابک</label>
            <input type="text" id="shabak_number" name="shabak_number" placeholder="شماره شابک" class="form-input border p-2 w-full" value="{{ old('shabak_number') }}">
            @error('shabak_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- شماره فیپا -->
        <div>
            <label for="fipa_number" class="form-label">شماره فیپا</label>
            <input type="text" id="fipa_number" name="fipa_number" placeholder="شماره فیپا" class="form-input border p-2 w-full" value="{{ old('fipa_number') }}">
            @error('fipa_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- وزن -->
        <div>
            <label for="weight" class="form-label">وزن (گرم)</label>
            <input type="number" id="weight" name="weight" placeholder="وزن (گرم)" class="form-input border p-2 w-full" value="{{ old('weight') }}">
            @error('weight') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- زبان -->
        <div>
            <label for="language_id" class="form-label">زبان</label>
            <select id="language_id" name="language_id" class="form-select border p-2 w-full" required>
                <option value="" disabled selected>زبان را انتخاب کنید</option>
                @foreach($languages as $lang)
                    <option value="{{ $lang->id }}" {{ old('language_id') == $lang->id ? 'selected' : '' }}>{{ $lang->title }}</option>
                @endforeach
            </select>
            @error('language_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- دسته‌بندی -->
        <div>
            <label for="category_id" class="form-label">دسته‌بندی</label>
            <select id="category_id" name="category_id" class="form-select border p-2 w-full" required>
                <option value="" disabled selected>دسته‌بندی را انتخاب کنید</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->title }}</option>
                @endforeach
            </select>
            @error('category_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- موضوع -->
        <div>
            <label for="subject_id" class="form-label">موضوع</label>
            <select id="subject_id" name="subject_id" class="form-select border p-2 w-full">
                <option value="" disabled selected>موضوع را انتخاب کنید</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->title }}</option>
                @endforeach
            </select>
            @error('subject_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- لهجه -->
        <div>
            <label for="accent_id" class="form-label">لهجه</label>
            <select id="accent_id" name="accent_id" class="form-select border p-2 w-full">
                <option value="" disabled selected>لهجه را انتخاب کنید</option>
                @foreach($accents as $accent)
                    <option value="{{ $accent->id }}" {{ old('accent_id') == $accent->id ? 'selected' : '' }}>{{ $accent->title }}</option>
                @endforeach
            </select>
            @error('accent_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- گروه سنی -->
        <div>
            <label for="age_group_id" class="form-label">گروه سنی</label>
            <select id="age_group_id" name="age_group_id" class="form-select border p-2 w-full">
                <option value="" disabled selected>گروه سنی</option>
                @foreach($age_groups as $age)
                    <option value="{{ $age->id }}" {{ old('age_group_id') == $age->id ? 'selected' : '' }}>{{ $age->title }}</option>
                @endforeach
            </select>
            @error('age_group_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- فایل صوتی -->
        <div>
            <label for="audio_file" class="form-label">فایل صوتی</label>
            <select id="audio_file" name="audio_file" class="form-select border p-2 w-full">
                <option value="0" {{ old('audio_file') == 0 ? 'selected' : '' }}>بدون فایل صوتی</option>
                <option value="1" {{ old('audio_file') == 1 ? 'selected' : '' }}>فایل صوتی دارد</option>
            </select>
            @error('audio_file') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- فایل ویدئویی -->
        <div>
            <label for="video_file" class="form-label">فایل ویدئویی</label>
            <select id="video_file" name="video_file" class="form-select border p-2 w-full">
                <option value="0" {{ old('video_file') == 0 ? 'selected' : '' }}>بدون فایل ویدئویی</option>
                <option value="1" {{ old('video_file') == 1 ? 'selected' : '' }}>فایل ویدئویی دارد</option>
            </select>
            @error('video_file') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- لهجه ویدئویی -->
        <div>
            <label for="video_accent" class="form-label">لهجه ویدئویی</label>
            <select id="video_accent" name="video_accent" class="form-select border p-2 w-full">
                <option value="0" {{ old('video_accent') == 0 ? 'selected' : '' }}>ندارد</option>
                <option value="1" {{ old('video_accent') == 1 ? 'selected' : '' }}>دارد</option>
            </select>
            @error('video_accent') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- لهجه صوتی -->
        <div>
            <label for="audio_accent" class="form-label">لهجه صوتی</label>
            <select id="audio_accent" name="audio_accent" class="form-select border p-2 w-full">
                <option value="0" {{ old('audio_accent') == 0 ? 'selected' : '' }}>ندارد</option>
                <option value="1" {{ old('audio_accent') == 1 ? 'selected' : '' }}>دارد</option>
            </select>
            @error('audio_accent') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- دانلود -->
        <div>
            <label for="is_download" class="form-label">دانلود</label>
            <select id="is_download" name="is_download" class="form-select border p-2 w-full" onchange="toggleFileInput(this)">
                <option value="0" {{ old('is_download') == 0 ? 'selected' : '' }}>بدون امکان دانلود</option>
                <option value="1" {{ old('is_download') == 1 ? 'selected' : '' }}>قابل دانلود</option>
            </select>
            @error('is_download') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- فایل قابل دانلود -->
        <div id="fileInputContainer" style="display: {{ old('is_download') == 1 ? 'block' : 'none' }};">
            <label class="form-label">فایل کتاب 📄</label>
            <input type="file" id="fileInput" name="file" accept=".pdf,.epub,.mobi" class="hidden" onchange="previewFile(this, 'filePreview')">
            <button type="button" onclick="document.getElementById('fileInput').click()" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">
                ➕ افزودن فایل
            </button>
            <div id="filePreview" class="mt-4 relative"></div>
            @error('file') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- وضعیت کتاب -->
        <div>
            <label for="book_status" class="form-label">وضعیت کتاب</label>
            <select id="book_status" name="book_status" class="form-select border p-2 w-full">
                <option value="1" {{ old('book_status', 1) == 1 ? 'selected' : '' }}>فعال</option>
                <option value="0" {{ old('book_status') == 0 ? 'selected' : '' }}>غیرفعال</option>
            </select>
            @error('book_status') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- تاریخ انقضای تخفیف -->
        <div class="col-span-2">
            <label for="discount_expiration" class="form-label">📅 تاریخ انقضای تخفیف</label>
            <input type="datetime-local" id="discount_expiration" name="discount_expiration" class="form-input border p-2 rounded w-full" value="{{ old('discount_expiration') }}">
            @error('discount_expiration') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- هدف یادگیری -->
        <div>
            <label for="learning_subgoal_id" class="form-label">هدف یادگیری</label>
            <select id="learning_subgoal_id" name="learning_subgoal_id" class="form-select border p-2 w-full">
                <option value="" disabled selected>هدف یادگیری</option>
                @foreach($goals as $goal)
                    <option value="{{ $goal->id }}" {{ old('learning_subgoal_id') == $goal->id ? 'selected' : '' }}>{{ $goal->title }}</option>
                @endforeach
            </select>
            @error('learning_subgoal_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- سطح زبانی -->
        <div>
            <label for="language_levels_id" class="form-label">سطح زبان</label>
            <select id="language_levels_id" name="language_levels_id" class="form-select border p-2 w-full">
                <option value="" disabled selected>سطح زبان</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ old('language_levels_id') == $level->id ? 'selected' : '' }}>{{ $level->title }}</option>
                @endforeach
            </select>
            @error('language_levels_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- تصویر کاور -->
        <div class="col-span-2">
            <label class="form-label">تصویر کاور 📘</label>
            <input type="file" id="coverInput" name="cover" accept="image/*" class="hidden" onchange="previewSingleImage(this, 'coverPreview')">
            <button type="button" onclick="document.getElementById('coverInput').click()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                ➕ افزودن کاور
            </button>
            <div id="coverPreview" class="mt-4 relative"></div>
            @error('cover') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- فایل ویدیو -->
        <div class="col-span-2">
            <label class="form-label">ویدیو 📹</label>
            <input type="file" id="videoInput" name="video" accept="video/*" class="hidden" onchange="previewVideo(this, 'videoPreview')">
            <button type="button" onclick="document.getElementById('videoInput').click()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                ➕ افزودن ویدیو
            </button>
            <div id="videoPreview" class="mt-4 relative"></div>
            @error('video') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- تصویر اصلی -->
        <div class="col-span-2">
            <label class="form-label">تصویر اصلی 🖼️</label>
            <input type="file" id="imageInput" name="images" accept="image/*" class="hidden" onchange="previewSingleImage(this, 'imagePreview')">
            <button type="button" onclick="document.getElementById('imageInput').click()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                ➕ افزودن تصویر
            </button>
            <div id="imagePreview" class="mt-4 relative"></div>
            @error('images') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- تصاویر نمونه -->
        <div class="col-span-2">
            <label class="form-label">تصاویر نمونه 📑</label>
            <input type="file" id="sampleImagesInput" name="sample_images[]" accept="image/*" multiple class="hidden" onchange="previewMultipleImages(this, 'sampleImagesPreview')">
            <button type="button" onclick="document.getElementById('sampleImagesInput').click()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                ➕ افزودن تصاویر نمونه
            </button>
            <div id="sampleImagesPreview" class="mt-4 grid grid-cols-3 gap-4"></div>
            @error('sample_images.*') <span class="text-danger text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="col-span-2 bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
            ✅ ثبت کتاب
        </button>
    </form>
</div>

<div class="container mt-5">
    <h1 class="mb-4 text-center">لیست کتاب‌ها</h1>

    <!-- فرم جستجو -->
    <form method="GET" action="{{ route('add_book') }}" class="mb-4 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="جستجوی عنوان کتاب..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">جستجو</button>
    </form>

    <!-- جدول کتاب‌ها -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>عنوان</th>
                <th>نویسنده</th>
                <th>قیمت</th>
                <th>دسته‌بندی</th>
                <th>زبان</th>
                <th>وضعیت</th>
                <th>موجودی</th>
                <th>وضعیت کتاب</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($books as $book)
                <tr>
                    <td>{{ $book->id }}</td>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->author ?? '—' }}</td>
                    <td>{{ $book->productSeller ? number_format($book->productSeller->price) . ' تومان' : 'بدون قیمت' }}</td>
                    <td>{{ $book->category->title ?? '—' }}</td>
                    <td>{{ $book->language->title ?? '—' }}</td>
                    <td>{{ $book->productSeller ? $book->productSeller->quantity : 'ناموجود' }}</td>
                    <td>{{ $book->book_status ? 'فعال' : 'غیرفعال' }}</td>
                    <td>
                        <button class="btn btn-sm btn-info me-1" onclick='openEditBookModal({{ json_encode($book) }})'>
                            ویرایش
                        </button>
                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این کتاب مطمئن هستید؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">هیچ کتابی یافت نشد.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- صفحه‌بندی -->
    <div class="mt-4">
        {{ $books->appends(['search' => request('search')])->links() }}
    </div>
</div>

<!-- مدال ویرایش کتاب -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="editBookForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">ویرایش کتاب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- عنوان کتاب -->
                        <div class="col-md-6">
                            <label for="edit_title" class="form-label">عنوان</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                            @error('title') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- نویسنده -->
                        <div class="col-md-6">
                            <label for="edit_author" class="form-label">نویسنده</label>
                            <input type="text" class="form-control" id="edit_author" name="author">
                            @error('author') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- قیمت -->
                        <div class="col-md-6">
                            <label for="edit_price" class="form-label">قیمت</label>
                            <input type="number" class="form-control" id="edit_price" name="price" required>
                            @error('price') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- قیمت با تخفیف -->
                        <div class="col-md-6">
                            <label for="edit_off_price" class="form-label">قیمت با تخفیف</label>
                            <input type="number" class="form-control" id="edit_off_price" name="off_price">
                            @error('off_price') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- موجودی -->
                        <div class="col-md-6">
                            <label for="edit_quantity" class="form-label">موجودی</label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity" required>
                            @error('quantity') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- نو یا دست‌دوم -->

                        <!-- تعداد جلد -->
                        <div class="col-md-6">
                            <label for="edit_Volume_number" class="form-label">تعداد جلد</label>
                            <input type="number" class="form-control" id="edit_Volume_number" name="Volume_number">
                            @error('Volume_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- تعداد صفحات -->
                        <div class="col-md-6">
                            <label for="edit_page_number" class="form-label">تعداد صفحات</label>
                            <input type="number" class="form-control" id="edit_page_number" name="page_number">
                            @error('page_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- سال انتشار -->
                        <div class="col-md-6">
                            <label for="edit_year_of_publication" class="form-label">سال انتشار</label>
                            <input type="number" class="form-control" id="edit_year_of_publication" name="year_of_publication">
                            @error('year_of_publication') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- ابعاد -->
                        <div class="col-md-6">
                            <label for="edit_Dimensions" class="form-label">ابعاد (مثال: 25x15x5)</label>
                            <input type="text" class="form-control" id="edit_Dimensions" name="Dimensions">
                            @error('Dimensions') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- زمان چاپ -->
                        <div class="col-md-6">
                            <label for="edit_Time_to_print" class="form-label">زمان چاپ (روز)</label>
                            <input type="number" class="form-control" id="edit_Time_to_print" name="Time_to_print">
                            @error('Time_to_print') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- نوع کاغذ -->
                        <div class="col-md-6">
                            <label for="edit_Paper_type" class="form-label">نوع کاغذ</label>
                            <input type="text" class="form-control" id="edit_Paper_type" name="Paper_type">
                            @error('Paper_type') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- نوع پوشش -->
                        <div class="col-md-6">
                            <label for="edit_Cover_type" class="form-label">نوع پوشش</label>
                            <input type="text" class="form-control" id="edit_Cover_type" name="Cover_type">
                            @error('Cover_type') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- شماره شابک -->
                        <div class="col-md-6">
                            <label for="edit_shabak_number" class="form-label">شماره شابک</label>
                            <input type="text" class="form-control" id="edit_shabak_number" name="shabak_number">
                            @error('shabak_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- شماره فیپا -->
                        <div class="col-md-6">
                            <label for="edit_fipa_number" class="form-label">شماره فیپا</label>
                            <input type="text" class="form-control" id="edit_fipa_number" name="fipa_number">
                            @error('fipa_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- وزن -->
                        <div class="col-md-6">
                            <label for="edit_weight" class="form-label">وزن (گرم)</label>
                            <input type="number" class="form-control" id="edit_weight" name="weight">
                            @error('weight') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- زبان -->
                        <div class="col-md-6">
                            <label for="edit_language_id" class="form-label">زبان</label>
                            <select class="form-select" id="edit_language_id" name="language_id" required>
                                @foreach($languages as $lang)
                                    <option value="{{ $lang->id }}">{{ $lang->title }}</option>
                                @endforeach
                            </select>
                            @error('language_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- دسته‌بندی -->
                        <div class="col-md-6">
                            <label for="edit_category_id" class="form-label">دسته‌بندی</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- موضوع -->
                        <div class="col-md-6">
                            <label for="edit_subject_id" class="form-label">موضوع</label>
                            <select class="form-select" id="edit_subject_id" name="subject_id">
                                <option value="" disabled selected>موضوع را انتخاب کنید</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->title }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- لهجه -->
                        <div class="col-md-6">
                            <label for="edit_accent_id" class="form-label">لهجه</label>
                            <select class="form-select" id="edit_accent_id" name="accent_id">
                                <option value="" disabled selected>لهجه را انتخاب کنید</option>
                                @foreach($accents as $accent)
                                    <option value="{{ $accent->id }}">{{ $accent->title }}</option>
                                @endforeach
                            </select>
                            @error('accent_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- گروه سنی -->
                        <div class="col-md-6">
                            <label for="edit_age_group_id" class="form-label">گروه سنی</label>
                            <select class="form-select" id="edit_age_group_id" name="age_group_id">
                                <option value="" disabled selected>گروه سنی</option>
                                @foreach($age_groups as $age)
                                    <option value="{{ $age->id }}">{{ $age->title }}</option>
                                @endforeach
                            </select>
                            @error('age_group_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- فایل صوتی -->
                        <div class="col-md-6">
                            <label for="edit_audio_file" class="form-label">فایل صوتی</label>
                            <select class="form-select" id="edit_audio_file" name="audio_file">
                                <option value="0">بدون فایل صوتی</option>
                                <option value="1">فایل صوتی دارد</option>
                            </select>
                            @error('audio_file') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- فایل ویدئویی -->
                        <div class="col-md-6">
                            <label for="edit_video_file" class="form-label">فایل ویدئویی</label>
                            <select class="form-select" id="edit_video_file" name="video_file">
                                <option value="0">بدون فایل ویدئویی</option>
                                <option value="1">فایل ویدئویی دارد</option>
                            </select>
                            @error('video_file') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- لهجه ویدئویی -->
                        <div class="col-md-6">
                            <label for="edit_video_accent" class="form-label">لهجه ویدئویی</label>
                            <select class="form-select" id="edit_video_accent" name="video_accent">
                                <option value="0">ندارد</option>
                                <option value="1">دارد</option>
                            </select>
                            @error('video_accent') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- لهجه صوتی -->
                        <div class="col-md-6">
                            <label for="edit_audio_accent" class="form-label">لهجه صوتی</label>
                            <select class="form-select" id="edit_audio_accent" name="audio_accent">
                                <option value="0">ندارد</option>
                                <option value="1">دارد</option>
                            </select>
                            @error('audio_accent') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- دانلود -->
                        <div class="col-md-6">
                            <label for="edit_is_download" class="form-label">دانلود</label>
                            <select class="form-select" id="edit_is_download" name="is_download" onchange="toggleEditFileInput(this)">
                                <option value="0">بدون امکان دانلود</option>
                                <option value="1">قابل دانلود</option>
                            </select>
                            @error('is_download') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- فایل قابل دانلود -->
                        <div class="col-md-6" id="editFileInputContainer">
                            <label class="form-label">فایل کتاب 📄</label>
                            <input type="file" id="edit_fileInput" name="file" accept=".pdf,.epub,.mobi" class="hidden" onchange="previewFile(this, 'edit_filePreview')">
                            <button type="button" onclick="document.getElementById('edit_fileInput').click()" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">
                                ➕ افزودن فایل جدید
                            </button>
                            <div id="edit_filePreview" class="mt-4 relative"></div>
                            @error('file') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- وضعیت کتاب -->
                        <div class="col-md-6">
                            <label for="edit_book_status" class="form-label">وضعیت کتاب</label>
                            <select class="form-select" id="edit_book_status" name="book_status">
                                <option value="1">فعال</option>
                                <option value="0">غیرفعال</option>
                            </select>
                            @error('book_status') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- تاریخ انقضای تخفیف -->
                        <div class="col-md-12">
                            <label for="edit_discount_expiration" class="form-label">📅 تاریخ انقضای تخفیف</label>
                            <input type="datetime-local" class="form-control" id="edit_discount_expiration" name="discount_expiration">
                            @error('discount_expiration') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- هدف یادگیری -->
                        <div class="col-md-6">
                            <label for="edit_learning_subgoal_id" class="form-label">هدف یادگیری</label>
                            <select class="form-select" id="edit_learning_subgoal_id" name="learning_subgoal_id">
                                <option value="" disabled selected>هدف یادگیری</option>
                                @foreach($goals as $goal)
                                    <option value="{{ $goal->id }}">{{ $goal->title }}</option>
                                @endforeach
                            </select>
                            @error('learning_subgoal_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- سطح زبانی -->
                        <div class="col-md-6">
                            <label for="edit_language_levels_id" class="form-label">سطح زبان</label>
                            <select class="form-select" id="edit_language_levels_id" name="language_levels_id">
                                <option value="" disabled selected>سطح زبان</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->title }}</option>
                                @endforeach
                            </select>
                            @error('language_levels_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- تصویر کاور -->
                        <div class="col-md-12">
                            <label class="form-label">تصویر کاور 📘</label>
                            <input type="file" id="edit_coverInput" name="cover" accept="image/*" class="hidden" onchange="previewSingleImage(this, 'edit_coverPreview')">
                            <button type="button" onclick="document.getElementById('edit_coverInput').click()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                ➕ افزودن کاور جدید
                            </button>
                            <div id="edit_coverPreview" class="mt-4 relative"></div>
                            @error('cover') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- فایل ویدیو -->
                        <div class="col-md-12">
                            <label class="form-label">ویدیو 📹</label>
                            <input type="file" id="edit_videoInput" name="video" accept="video/*" class="hidden" onchange="previewVideo(this, 'edit_videoPreview')">
                            <button type="button" onclick="document.getElementById('edit_videoInput').click()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                ➕ افزودن ویدیوی جدید
                            </button>
                            <div id="edit_videoPreview" class="mt-4 relative"></div>
                            @error('video') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- تصویر اصلی -->
                        <div class="col-md-12">
                            <label class="form-label">تصویر اصلی 🖼️</label>
                            <input type="file" id="edit_imageInput" name="images" accept="image/*" class="hidden" onchange="previewSingleImage(this, 'edit_imagePreview')">
                            <button type="button" onclick="document.getElementById('edit_imageInput').click()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                                ➕ افزودن تصویر جدید
                            </button>
                            <div id="edit_imagePreview" class="mt-4 relative"></div>
                            @error('images') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <!-- تصاویر نمونه -->
                        <div class="col-md-12">
                            <label class="form-label">تصاویر نمونه 📑</label>
                            <input type="file" id="edit_sampleImagesInput" name="sample_images[]" accept="image/*" multiple class="hidden" onchange="previewMultipleImages(this, 'edit_sampleImagesPreview')">
                            <button type="button" onclick="document.getElementById('edit_sampleImagesInput').click()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                                ➕ افزودن تصاویر نمونه جدید
                            </button>
                            <div id="edit_sampleImagesPreview" class="mt-4 grid grid-cols-3 gap-4"></div>
                            @error('sample_images.*') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('admin.downad')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // پیش‌نمایش یک تصویر (کاور، تصویر اصلی)
    function previewSingleImage(input, previewId) {
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'relative';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full preview-img rounded shadow';

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '🗑';
                removeBtn.type = 'button';
                removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600';
                removeBtn.onclick = () => {
                    input.value = '';
                    preview.innerHTML = '';
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // پیش‌نمایش ویدیو
    function previewVideo(input, previewId) {
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(input.files[0]);
            video.controls = true;
            video.className = 'w-full h-auto rounded shadow';

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '🗑';
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600';
            removeBtn.onclick = () => {
                input.value = '';
                preview.innerHTML = '';
            };

            const wrapper = document.createElement('div');
            wrapper.className = 'relative';
            wrapper.appendChild(video);
            wrapper.appendChild(removeBtn);
            preview.appendChild(wrapper);
        }
    }

    // پیش‌نمایش فایل قابل دانلود
    function previewFile(input, previewId) {
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative';

            const fileName = document.createElement('span');
            fileName.textContent = input.files[0].name;
            fileName.className = 'text-sm';

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '🗑';
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600';
            removeBtn.onclick = () => {
                input.value = '';
                preview.innerHTML = '';
            };

            wrapper.appendChild(fileName);
            wrapper.appendChild(removeBtn);
            preview.appendChild(wrapper);
        }
    }

    // پیش‌نمایش چند تصویر (تصاویر نمونه)
    function previewMultipleImages(input, previewId) {
        const preview = document.getElementById(previewId);

        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full preview-img rounded shadow';

                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '🗑';
                    removeBtn.type = 'button';
                    removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600';
                    removeBtn.onclick = () => {
                        wrapper.remove();
                        const dt = new DataTransfer();
                        Array.from(input.files).forEach(f => {
                            if (f !== file) dt.items.add(f);
                        });
                        input.files = dt.files;
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(removeBtn);
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // نمایش فایل‌های قبلی
    function displayExistingFile(path, previewId, type, fileId = null) {
        const preview = document.getElementById(previewId);
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = path.startsWith('http') ? path : `/${path}`;
            img.className = 'w-full preview-img rounded shadow';
            wrapper.appendChild(img);
        } else if (type === 'video') {
            const video = document.createElement('video');
            video.src = path.startsWith('http') ? path : `/${path}`;
            video.controls = true;
            video.className = 'w-full h-auto rounded shadow';
            wrapper.appendChild(video);
        } else if (type === 'file') {
            const fileName = document.createElement('span');
            fileName.textContent = path.split('/').pop();
            fileName.className = 'text-sm';
            wrapper.appendChild(fileName);
        }

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '🗑';
        removeBtn.type = 'button';
        removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600';
        removeBtn.onclick = () => {
            wrapper.remove();
            if (fileId) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `delete_files[]`;
                input.value = fileId;
                document.getElementById('editBookForm').appendChild(input);
            }
        };

        wrapper.appendChild(removeBtn);
        preview.appendChild(wrapper);
    }

    // کنترل نمایش فیلد فایل در فرم افزودن
    function toggleFileInput(select) {
        const fileInputContainer = document.getElementById('fileInputContainer');
        fileInputContainer.style.display = select.value == '1' ? 'block' : 'none';
    }

    // کنترل نمایش فیلد فایل در فرم ویرایش
    function toggleEditFileInput(select) {
        const fileInputContainer = document.getElementById('editFileInputContainer');
        fileInputContainer.style.display = select.value == '1' ? 'block' : 'none';
    }

    // باز کردن مدال ویرایش کتاب
    function openEditBookModal(book) {
        document.getElementById('edit_title').value = book.title || '';
        document.getElementById('edit_author').value = book.author || '';
        document.getElementById('edit_price').value = book.product_seller ? book.product_seller.price : '';
        document.getElementById('edit_off_price').value = book.product_seller ? book.product_seller.discounted_price : '';
        document.getElementById('edit_quantity').value = book.product_seller ? book.product_seller.quantity : '';
        document.getElementById('edit_Volume_number').value = book.Volume_number || '';
        document.getElementById('edit_page_number').value = book.page_number || '';
        document.getElementById('edit_year_of_publication').value = book.year_of_publication || '';
        document.getElementById('edit_Dimensions').value = book.Dimensions || '';
        document.getElementById('edit_Time_to_print').value = book.Time_to_print || '';
        document.getElementById('edit_Paper_type').value = book.Paper_type || '';
        document.getElementById('edit_Cover_type').value = book.Cover_type || '';
        document.getElementById('edit_shabak_number').value = book.shabak_number || '';
        document.getElementById('edit_fipa_number').value = book.fipa_number || '';
        document.getElementById('edit_weight').value = book.weight || '';
        document.getElementById('edit_language_id').value = book.language_id || '';
        document.getElementById('edit_category_id').value = book.category_id || '';
        document.getElementById('edit_subject_id').value = book.subject_id || '';
        document.getElementById('edit_accent_id').value = book.accent_id || '';
        document.getElementById('edit_age_group_id').value = book.age_group_id || '';
        document.getElementById('edit_audio_file').value = book.audio_file || 0;
        document.getElementById('edit_video_file').value = book.video_file || 0;
        document.getElementById('edit_video_accent').value = book.video_accent || 0;
        document.getElementById('edit_audio_accent').value = book.audio_accent || 0;
        document.getElementById('edit_is_download').value = book.is_download || 0;
        document.getElementById('edit_book_status').value = book.book_status || 1;
        document.getElementById('edit_discount_expiration').value = book.product_seller && book.product_seller.discount_expire_at ? new Date(book.product_seller.discount_expire_at).toISOString().slice(0, 16) : '';
        document.getElementById('edit_learning_subgoal_id').value = book.learning_subgoal_id || '';
        document.getElementById('edit_language_levels_id').value = book.language_levels_id || '';

        document.getElementById('editBookForm').action = `/admindashboard/books/${book.id}`;

        // پاک کردن پیش‌نمایش‌های قبلی
        document.getElementById('edit_coverPreview').innerHTML = '';
        document.getElementById('edit_videoPreview').innerHTML = '';
        document.getElementById('edit_imagePreview').innerHTML = '';
        document.getElementById('edit_sampleImagesPreview').innerHTML = '';
        document.getElementById('edit_filePreview').innerHTML = '';

        // نمایش فایل‌های قبلی
        if (book.cover && book.cover.path) {
            displayExistingFile(book.cover.path, 'edit_coverPreview', 'image', book.cover.id || null);
        }
        if (book.videos && book.videos.length > 0 && book.videos[0].path) {
            displayExistingFile(book.videos[0].path, 'edit_videoPreview', 'video', book.videos[0].id || null);
        }
        if (book.images && book.images.length > 0 && book.images[0].path) {
            displayExistingFile(book.images[0].path, 'edit_imagePreview', 'image', book.images[0].id || null);
        }
        if (book.sample_images && Array.isArray(book.sample_images)) {
            book.sample_images.forEach(sample => {
                if (sample.path) {
                    displayExistingFile(sample.path, 'edit_sampleImagesPreview', 'image', sample.id || null);
                }
            });
        }
        if (book.file) {
            displayExistingFile(book.file, 'edit_filePreview', 'file');
        }

        // تنظیم نمایش فیلد فایل
        toggleEditFileInput(document.getElementById('edit_is_download'));

        const editModal = new bootstrap.Modal(document.getElementById('editBookModal'));
        editModal.show();
    }
</script>
</body>
</html>
