<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
  
    /**
     * @OA\Get(
     *     path="/news_project/public/api/articles",
     *     operationId="getArticles",
     *     tags={"Articles"},
     *     summary="Get paginated articles",
     *     description="Fetch a list of articles with pagination and filtering options.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search in title and content",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter articles by published date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         description="Filter articles by author",
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
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function index(Request $request)
    {
        $articles = Article::when($request->keyword, function ($query, $keyword) {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('title', 'like', "%$keyword%")
                         ->orWhere('content', 'like', "%$keyword%");
            });
        })
        ->when($request->date, function ($query, $date) {
            $query->whereDate('published_date', $date);
        })
        ->when($request->author, function ($query, $author) {
            $query->where('author', $author);
        })
        ->when($request->source, function ($query, $source) {
            $query->where('source', $source);
        })
        ->paginate(10);
        return ArticleResource::collection($articles);
    }

    /**
     * @OA\Get(
     *     path="/news_project/public/api/articles/{article}",
     *     operationId="getArticleDetails",
     *     tags={"Articles"},
     *     summary="Get article details",
     *     description="Retrieve details of a single article.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

}


