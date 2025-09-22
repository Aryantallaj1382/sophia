@include('admin.upad')

<div class="container mt-4">
    <h2 class="mb-4">مدیریت محصولات آموزشی</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('educational-products.create') }}" class="btn btn-primary">افزودن محصول جدید</a>
    </div>

    @if($products->count())
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
            <tr>
                <th>عنوان</th>
                <th>گروه سنی</th>
                <th>زبان</th>
                <th>قیمت</th>
                <th>تخفیف (%)</th>
                <th>تاریخ انقضای تخفیف</th>
                <th>ارسال رایگان</th>
                <th>ارسال امروز</th>
                <th>دانلودی</th>
                <th>فروشندگان</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->ageGroup->title ?? '---' }}</td>
                    <td>{{ $product->language->name ?? '---' }}</td>
                    <td>{{ number_format($product->price) }} تومان</td>
                    <td>{{ $product->discount_percentage ?? '-' }}</td>
                    <td>{{ $product->discount_expiration ?? '-' }}</td>
                    <td>{{ $product->free_shipping ? 'بله' : 'خیر' }}</td>
                    <td>{{ $product->today_shipping ? 'بله' : 'خیر' }}</td>
                    <td>{{ $product->is_download ? 'بله' : 'خیر' }}</td>
                    <td>
                        @foreach($product->sellers as $seller)
                            <span class="badge bg-secondary">{{ $seller->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('educational-products.edit', $product->id) }}" class="btn btn-warning btn-sm">ویرایش</a>
                        <form action="{{ route('educational-products.destroy', $product->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('آیا مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">هیچ محصولی یافت نشد.</div>
    @endif
</div>
