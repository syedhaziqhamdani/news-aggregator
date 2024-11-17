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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('published_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->keyword}%")
                  ->orWhere('description', 'LIKE', "%{$request->keyword}%");
            });
        }

        $articles = $query->paginate(10);

        // Wrap the response in the resource
        return new ArticleCollection($articles);
    }

    public function personalizedFeed(Request $request)
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found.'], 404);
        }

        $query = Article::query();

        if (!empty($preferences->sources)) {
            $query->whereIn('source', $preferences->sources);
        }

        if (!empty($preferences->categories)) {
            $query->whereIn('category', $preferences->categories);
        }

        if (!empty($preferences->authors)) {
            $query->whereIn('author', $preferences->authors);
        }

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->keyword}%")
                ->orWhere('description', 'LIKE', "%{$request->keyword}%");
            });
        }

        $articles = $query->paginate(10);

        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);

        return response()->json($article);
    }
}
