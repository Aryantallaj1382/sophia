<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');

        $blogQuery = Blog::latest()->select(['id','image', 'title' ,'reading_time' , 'views' , 'content']);
        $blog1Query = Blog::latest()->where('type', 'blog')->select(['id','image', 'title' ,'reading_time' , 'views' , 'content']);
        $newsQuery = Blog::latest()->where('type', 'news')->select(['id','image', 'title' ,'reading_time' , 'views' , 'content']);

        if ($category && $category !== 'all') {
            $blogQuery->where('category', $category);
            $blog1Query->where('category', $category);
            $newsQuery->where('category', $category);
        }

        $blog = $blogQuery->take(5)->get();
        $blog1 = $blog1Query->take(10)->get();
        $news = $newsQuery->take(10)->get();

        return api_response([
            'blog' => $blog,
            'blog1' => $blog1,
            'news' => $news,
        ]);
    }

    public function index2(Request $request)
    {
        $category = $request->input('category');

        $blogQuery = Blog::latest();

        if ($category && $category !== 'all') {
            $blogQuery->where('category', $category);
        }

        $blog = $blogQuery->paginate(12);

        return api_response($blog);
    }

    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        return api_response($blog);

    }
}
