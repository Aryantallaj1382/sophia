<?php

namespace App\Http\Controllers\Api\Interest;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type');

        $query = Like::where('user_id', $user->id);

        if ($type) {
            $map = [
                'one_to_one' => \App\Models\Professor::class,
                'group'   => \App\Models\GroupClass::class,
                'webinar' => \App\Models\Webinar::class,
                'books' => \App\Models\Book::class,
                'mock_test' => \App\Models\Exam::class,
            ];

            if (isset($map[$type])) {
                $query->where('likeable_type', $map[$type]);
            }
        }

        $likes = $query->paginate();
         $likes->getCollection()->transform(function ($item) {
            return [
                'id'            => $item->likeable->id,
                'user_id'       => $item->user_id,
                'exam_type'       => $item->likeable?->type ?? null,
                'date' =>           $item?->created_at?->format('d M') ?? null,
                'time' =>           $item?->created_at?->format('H:i') ?? null,
                'title'         => $item->likeable->title ?? $item->likeable->name ?? null,
                'image' => $item->likeable->image ?? $item->likeable?->user?->profile,
                'likeable_type' => class_basename($item->likeable_type),
            ];
        });

        return api_response($likes);
    }

}
