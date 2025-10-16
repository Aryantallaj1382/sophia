@extends('admin.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">


            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- هدر -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">ایجاد بلاگ جدید</h2>
                <a href="{{ route('admin.blogs.index') }}"
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm">
                    بازگشت
                </a>
            </div>

            <!-- فرم -->
            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <!-- عنوان -->
                <div>
                    <label for="title" class="block text-gray-700 font-medium mb-2">عنوان بلاگ</label>
                    <input type="text" name="title" id="title"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500"
                           value="{{ old('title') }}" placeholder="مثلاً: تاثیر یادگیری زبان دوم">
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- توضیحات کوتاه -->
                <div>
                    <label for="excerpt" class="block text-gray-700 font-medium mb-2">توضیحات کوتاه</label>
                    <textarea name="excerpt" id="excerpt" rows="2"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500">{{ old('excerpt') }}</textarea>
                    @error('excerpt') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- متن کامل -->
                <!-- متن کامل -->
                <div>
                    <label for="content" class="block text-gray-700 font-medium mb-2">متن بلاگ</label>
                    <textarea name="content" id="content" class="hidden">{{ old('content') }}</textarea>
                    @error('content') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- CKEditor -->
                <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
                <script>
                    ClassicEditor
                        .create(document.querySelector('#content'), {
                            toolbar: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'highlight', '|',
                                'fontColor', 'fontBackgroundColor', 'fontSize', 'fontFamily', '|',
                                'link', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', 'alignment', '|',
                                'blockQuote', 'codeBlock', 'insertTable', 'horizontalLine', 'mediaEmbed', 'imageUpload', '|',
                                'undo', 'redo', 'findAndReplace', 'removeFormat'
                            ],
                            language: 'fa',
                            placeholder: 'متن بلاگ خود را اینجا بنویسید...',
                            image: {
                                toolbar: [
                                    'imageTextAlternative', 'imageStyle:full', 'imageStyle:side'
                                ]
                            },
                            table: {
                                contentToolbar: [
                                    'tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties'
                                ]
                            },
                            mediaEmbed: {
                                previewsInData: true
                            }
                        })
                        .then(editor => {
                            const editable = editor.ui.view.editable.element;
                            editable.classList.add('rounded-lg', 'shadow-sm', 'border', 'border-gray-300', 'p-4');
                            editable.style.minHeight = '500px';
                            editable.style.backgroundColor = 'white';
                        })
                        .catch(error => {
                            console.error(error);
                        });
                </script>

                <!-- زمان مطالعه -->
                <div>
                    <label for="reading_time" class="block text-gray-700 font-medium mb-2">زمان مطالعه (به دقیقه)</label>
                    <input type="text" name="reading_time" id="reading_time"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500"
                           value="{{ old('reading_time') }}" placeholder="مثلاً: 5 دقیقه">
                    @error('reading_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- تصویر -->
                <div>
                    <label for="image" class="block text-gray-700 font-medium mb-2">تصویر شاخص</label>
                    <input type="file" name="image" id="image"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500">
                    <p class="text-gray-400 text-sm mt-1">ابعاد پیشنهادی: 800x400 پیکسل</p>
                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- دسته‌بندی و نوع -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category" class="block text-gray-700 font-medium mb-2">دسته‌بندی</label>
                        <select name="category" id="category"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500">
                            <option value="">انتخاب دسته‌بندی</option>
                            @foreach(['one_to_one','group','webinar','placement_test','mock_test','final_exam'] as $cat)
                                <option value="{{ $cat }}" {{ old('category')==$cat ? 'selected':'' }}>
                                    {{ ucfirst(str_replace('_',' ',$cat)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-gray-700 font-medium mb-2">نوع</label>
                        <select name="type" id="type"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500">
                            <option value="">انتخاب نوع</option>
                            @foreach(['blog','news'] as $type)
                                <option value="{{ $type }}" {{ old('type')==$type ? 'selected':'' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- تگ‌ها -->
                <div>
                    <label for="tags" class="block text-gray-700 font-medium mb-2">تگ‌ها</label>
                    <input type="text" id="tags" placeholder="تگ را وارد کنید و Enter بزنید"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500">
                    <p class="text-gray-400 text-sm mt-1">برای افزودن چند تگ، بعد از هر کلمه Enter بزنید</p>
                    <div id="tags-container" class="mt-3 flex flex-wrap gap-2"></div>
                </div>

                <!-- دکمه‌ها -->
                <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                    <button type="reset"
                            class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                        ریست
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600 shadow transition">
                        ایجاد بلاگ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- استایل تگ‌ها -->
    <style>
        .tag-badge {
            @apply bg-blue-600 text-white px-3 py-1 rounded-full text-sm flex items-center gap-2;
        }
        .tag-badge i {
            @apply cursor-pointer;
        }
    </style>

    <!-- اسکریپت تگ‌ها با دکمه × -->
    <script>
        const input = document.getElementById('tags');
        const container = document.getElementById('tags-container');
        let tags = [];

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && this.value.trim() !== '') {
                e.preventDefault();
                tags.push(this.value.trim());
                renderTags();
                this.value = '';
            }
        });

        function renderTags() {
            container.innerHTML = '';
            tags.forEach((tag, index) => {
                container.innerHTML += `
                <span class="tag-badge">
                    ${tag} <i onclick="removeTag(${index})">&times;</i>
                </span>
                <input type="hidden" name="tags[]" value="${tag}">
            `;
            });
        }

        function removeTag(i) {
            tags.splice(i, 1);
            renderTags();
        }
    </script>
@endsection
