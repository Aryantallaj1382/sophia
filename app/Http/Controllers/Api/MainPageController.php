<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Professor;
use App\Models\Slider;
use App\Models\Story;
use Illuminate\Http\Request;

class MainPageController extends Controller
{
    public function index()
    {


        $story = Story::where('main_page' , true)->latest()->take(20)->get()->map(function ($story) {
            return [
                'id' => $story->id,
                'cover' => $story->cover_image,

            ];
        });
        $slider = Slider::latest()->take(8)->get()->map(function ($slider) {
            return [
                'id' => $slider->id,
                'image' => $slider->image,
                'mobile_image' => $slider->mobile_image,
                'link' => $slider->link,
            ];
        });
        $professor = Professor::latest()->take(8)->get()->map(function ($professor) {
            return [
                'id' => $professor->id,
                'name' => $professor->user->name ?? $professor->name ?? null,
                'image' => $professor->user->profile ?? null,
                'date' => $professor->nearest_open_time,
            ];
        });
        $books = Book::latest()->take(8)->get()->map(function ($book) {
            return [
                'id' => $book->id,
                'name' => $book->name,
                'image' => $book->image,
                'ageGroups' => $book->ageGroups?->first()->title ?? null,
            ];
        });
        return api_response([
            'story' =>$story,
            'sliders' => $slider,
            'professors' => $professor,
            'books'=>$books,

        ]);

    }
}
