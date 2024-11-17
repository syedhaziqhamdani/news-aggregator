<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleCollection;

class ArticleController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->filled('keyword')) {
            $query->where('title', 'LIKE', "%{$request->keyword}%");
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $articles = $query->paginate(10);

        // Wrap the response in the resource
        return new ArticleCollection($articles);
    }


    public function show($id)
    {
        $article = Article::findOrFail($id);

        return response()->json($article);
    }
}
