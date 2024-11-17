<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleCollection;

/**
 * @OA\Tag(
 *     name="Articles",
 *     description="API Endpoints for managing articles"
 * )
 */
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Fetch paginated articles",
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search in articles",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter articles by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter articles by source",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = Article::query();

            if ($request->filled('keyword')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('description', 'LIKE', "%{$request->keyword}%");
                });
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

            $articles = $query->paginate(10);

            // Wrap the response in the resource
            return new ArticleCollection($articles);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch articles', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/user/news-feed",
     *     tags={"Articles"},
     *     summary="Fetch personalized news feed for authenticated user",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search in articles",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Personalized news feed fetched successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No preferences found"
     *     )
     * )
     */
    public function personalizedFeed(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch personalized news feed', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Fetch details of a single article",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $article = Article::findOrFail($id);

            return response()->json($article);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch article details', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
