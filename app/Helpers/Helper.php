<?php

use App\Models\ProfessorTimeSlot;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;

if (!function_exists('to_latin_digits')) {
    function to_latin_digits(string|null $str): string|null
    {
        if (blank($str))
            return $str;

        return str_replace(
            array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'),
            array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
            $str
        );
    }
}

if (!function_exists('persian_digits')) {
    function to_persian_digits(string|int|float|null $str): string|null
    {
        if ($str === null)
            return null;

        return str_replace(
            array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
            array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'),
            (string)$str
        );
    }
}
if (!function_exists('generateOrderCode')) {
    function generateOrderCode($userId)
    {
        $random = random_int(100, 999);
        return 1 . $random . str_pad($userId, 5, '0', STR_PAD_LEFT) . time();
    }
}

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
function api_response(mixed $data = [], string $message = '', int $status = 200): JsonResponse
{
    // اگر پگیند شده باشد
    if ($data instanceof LengthAwarePaginator) {
        return response()->json([
            'message' => $message,
            'data' => $data->items(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
        ], $status);
    }

    // اگر کالکشن یا Arrayable باشد
    if ($data instanceof Collection ) {
        $array = $data->toArray();

        // اگر چند آیتم داشت → داخل data
        if (count($array) > 1) {
            return response()->json([
                'message' => $message,
                'data' => $array
            ], $status);
        }

        // اگر فقط یک آیتم داشت → مستقیم
        if (count($array) === 1) {
            return response()->json(array_merge(['message' => $message], (array) $array[0]), $status);
        }

        // اگر خالی بود
        return response()->json(['message' => $message, 'data' => []], $status);
    }

    // اگر آرایه ساده‌ای از آبجکت‌ها باشد
//    if (is_array($data)) {
//        if (!empty($data) && collect($data)->every(fn($item) => is_array($item) || is_object($item))) {
//            // چند آیتم → داخل data
//            if (count($data) > 1) {
//                return response()->json(['message' => $message, 'data' => $data], $status);
//            }
//            // یک آیتم → مستقیم
//            return response()->json(array_merge(['message' => $message], (array) $data[0]), $status);
//        }
//
//        // اگر آرایه اسکالر
//        return response()->json(['message' => $message, 'data' => $data], $status);
//    }

    // اگر آبجکت بود → مستقیم
    if (is_object($data)) {
        return response()->json(array_merge(['message' => $message], (array) $data), $status);
    }

    // اگر اسکالر بود → مستقیم
    return response()->json(array_merge(['message' => $message], (array) $data), $status);
}


function generatePaginationLinks(LengthAwarePaginator $data)
{
    $links = [];
    $links[] = [
        'url' => $data->previousPageUrl(),
        'label' => '&laquo; Previous',
        'active' => $data->onFirstPage(),
    ];
    foreach (range(1, $data->lastPage()) as $page) {
        $links[] = [
            'url' => $data->url($page),
            'label' => (string)$page,
            'active' => $data->currentPage() === $page,
        ];
    }

    $links[] = [
        'url' => $data->nextPageUrl(),
        'label' => 'Next &raquo;',
        'active' => !$data->hasMorePages(),
    ];

    return $links;
}


function normalize_filename(string $filename): string
{
    $replacements = [
        'тАУ' => '-',
    ];

    return strtr($filename, $replacements);
}
function updateOpenSlots()
{
    $slots = ProfessorTimeSlot::doesntHave('privetClassReservations')->get();

    foreach ($slots as $slot) {
        $slot->status = 'open';
        $slot->save();
    }

    return response()->json([
        'message' => 'Time slots updated successfully!',
        'updated_count' => count($slots)
    ]);
}
