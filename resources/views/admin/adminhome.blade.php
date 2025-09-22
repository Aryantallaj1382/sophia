@include('admin.upad')

{{-- @if(session('success'))
    <div class="alert alert-success" id="successMessage">
        {{ session('success') }}
    </div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Fade out success message after 5 seconds
        if ($('#successMessage').length) {
            setTimeout(function() {
                $('#successMessage').fadeOut('slow');
            }, 5000);
        }

        // Edit functionality
        window.openEditModal = function(id) {
            fetch(`/edit_keshor/${id}`)
                .then(response => response.json())
                .then(data => {
                    $('#edit_id').val(data.id);
                    $('#edit_name_fa').val(data.name_fa);
                    $('#edit_name_en').val(data.name_en);
                    $('#editModal').modal('show');
                })
                .catch(err => {
                    alert('خطا در بارگذاری اطلاعات.'); // Error handling
                });
        };

        // Delete functionality
        window.deleteKeshor = function(id) {
            if (confirm('آیا از حذف این رکورد مطمئن هستید؟')) {
                fetch(`/delete_keshor/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('خطا در حذف رکورد.');
                    }
                })
                .catch(err => {
                    alert('خطا در حذف رکورد.'); // Error handling
                });
            }
        };

        // Bulk delete functionality
        $('#bulk-delete').on('click', function() {
            const ids = $('.select-item:checked').map(function() {
                return $(this).data('id');
            }).get();

            if (ids.length > 0 && confirm('آیا از حذف انتخاب شده‌ها مطمئن هستید؟')) {
                fetch('/bulk_delete_keshor', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('خطا در حذف رکوردها.');
                    }
                })
                .catch(err => {
                    alert('خطا در حذف رکوردها.'); // Error handling
                });
            }
        });

        // Toggle the bulk delete button visibility
        $('#select-all').on('change', function() {
            $('.select-item').prop('checked', this.checked);
            toggleBulkDeleteButton();
        });

        $('.select-item').on('change', function() {
            toggleBulkDeleteButton();
        });

        function toggleBulkDeleteButton() {
            const checkedItems = $('.select-item:checked');
            $('#bulk-delete').prop('disabled', checkedItems.length === 0);
            $('#bulk-delete').text(`حذف انتخاب شده‌ها (${checkedItems.length})`);
        }

        // Search functionality
        $('#filter-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.list tr').filter(function() {
                const name = $(this).children('td').eq(1).text().toLowerCase(); // Search based on Persian name
                $(this).toggle(name.indexOf(value) > -1);
            });
        });
    });
</script>

<div class="card mb-6">
    <div class="card-header">
        <h5 class="mb-0">اضافه کردن کشور</h5>
    </div>
    <div class="card-body bg-light">
        <form id="keshorForm" action="{{ url('add_keshor') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name_fa">نام کشور (فارسی)</label>
                <input class="form-control" id="name_fa" name="name_fa" type="text" placeholder="نام کشور (فارسی) را وارد کنید" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="name_en">نام کشور (انگلیسی)</label>
                <input class="form-control" id="name_en" name="name_en" type="text" placeholder="نام کشور (انگلیسی) را وارد کنید" required>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary" type="submit">ثبت</button>
            </div>
        </form>
    </div>
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">لیست کشورها</h5>
            <div class="d-flex align-items-center">
                <input type="text" id="filter-input" class="form-control me-2" placeholder="جستجو بر اساس نام">
                <button id="bulk-delete" class="btn btn-danger" disabled>حذف انتخاب شده‌ها</button>
            </div>
        </div>
        <div class="table-responsive scrollbar mt-4">
            <table class="table table-bordered table-striped fs--1 mb-0">
                <thead class="bg-200 text-900">
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th class="text-start">نام کشور (فارسی)</th>
                        <th class="text-start">نام کشور (انگلیسی)</th>
                        <th class="text-start">عملیات</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @foreach($keshors as $keshorItem)
                    <tr>
                        <td><input type="checkbox" class="select-item" data-id="{{ $keshorItem->id }}"></td>
                        <td class="text-start">{{ $keshorItem->name_fa }}</td>
                        <td class="text-start">{{ $keshorItem->name_en }}</td>
                        <td class="text-start">
                            <button class="btn p-0" type="button" onclick="openEditModal({{ $keshorItem->id }})">
                                <span class="text-500 fas fa-edit"></span>
                            </button>
                            <button class="btn p-0 ms-2" type="button" onclick="deleteKeshor({{ $keshorItem->id }})">
                                <span class="text-500 fas fa-trash-alt"></span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">ویرایش کشور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="{{ url('update_keshor') }}" method="POST">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name_fa" class="form-label">نام کشور (فارسی)</label>
                        <input type="text" class="form-control" id="edit_name_fa" name="name_fa" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_name_en" class="form-label">نام کشور (انگلیسی)</label>
                        <input type="text" class="form-control" id="edit_name_en" name="name_en" required>
                    </div>
                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

@include('admin.downad')
