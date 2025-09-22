<?php

namespace App\Http\Controllers\Api\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;

class ProfessorLibreryController extends Controller
{
    public function books()
    {
        $userId = auth()->id();

        $professor = Professor::where('user_id', $userId)->firstOrFail();

        $books = $professor->books()->paginate();

        // تغییر ساختار خروجی
        $books->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->name,
                'ageGroups' => $item->ageGroups?->first()->title ?? null,
                'images' => $item->image ?? null,
            ];
        });

        return api_response($books);
    }

}
