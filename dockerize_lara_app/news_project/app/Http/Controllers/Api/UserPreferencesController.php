<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UserPreferenceResource;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Models\UserPreferenceDetail;
use Illuminate\Support\Facades\Auth;

class UserPreferencesController extends Controller
{

/**
 * @OA\Post(
 *   path="/news_project/public/api/preferences",
 *   operationId="setPreferences",
 *   tags={"User Preferences"},
 *   summary="Set User Preferences",
 *   description="Allows authenticated users to set their preferences for news sources, authors, and categories.",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"name", "preferences"},
 *         @OA\Property(
 *           property="name",
 *           type="string",
 *           example="John Doe",
 *           description="The user's full name"
 *         ),
 *         @OA\Property(
 *           property="preferences",
 *           type="array",
 *           description="An array of user preferences.",
 *           @OA\Items(
 *             type="object",
 *             required={"source", "author"},
 *             @OA\Property(
 *               property="source",
 *               type="string",
 *               example="BBC News",
 *               description="The preferred news source"
 *             ),
 *             @OA\Property(
 *               property="author",
 *               type="string",
 *               example="John Doe",
 *               description="The preferred author"
 *             ),
 *             @OA\Property(
 *               property="category_id",
 *               type="integer",
 *               example=1,
 *               nullable=true,
 *               description="The preferred category ID (optional)"
 *             )
 *           )
 *         )
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Preferences set successfully.",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(
 *           type="object",
 *           @OA\Property(
 *             property="name",
 *             type="string",
 *             example="John Doe"
 *           ),
 *           @OA\Property(
 *             property="preferences",
 *             type="array",
 *             @OA\Items(
 *               type="object",
 *               @OA\Property(
 *                 property="source",
 *                 type="string",
 *                 example="BBC News"
 *               ),
 *               @OA\Property(
 *                 property="author",
 *                 type="string",
 *                 example="John Doe"
 *               ),
 *               @OA\Property(
 *                 property="category_id",
 *                 type="integer",
 *                 example=1
 *               )
 *             )
 *           )
 *         )
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=422,
 *     description="Validation Error",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Validation Error"
 *       ),
 *       @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\Property(
 *           type="array",
 *           @OA\Items(type="string")
 *         )
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=401,
 *     description="Unauthenticated",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Unauthenticated."
 *       )
 *     )
 *   )
 * )
 */

public function setPreferences(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'preferences' => 'array|required',
        'preferences.*.source' => 'required|string',
        'preferences.*.author' => 'required|string',
        'preferences.*.category_id' => 'nullable|exists:categories,id',
    ]);
    
    $user = Auth::user();
    
    // Update or create UserPreference
    $userPreference = UserPreference::updateOrCreate(
        ['user_id' => $user->id],
        $request->only(['name'])
    );
    
    // Insert UserPreferenceDetail if preferences exist
    if ($request->has('preferences')) {
        // Clear existing details (optional, based on your logic)
        $userPreference->details()->delete();
        // Add new details
        $userPreference->details()->createMany($request->input('preferences'));
    }
    return UserPreferenceResource::collection(collect([$userPreference]));
}

    /**
     * Get User Preferences
     * 
     * @OA\Get(
     *   path="/news_project/public/api/preferences",
     *   operationId="getPreferences",
     *   tags={"User Preferences"},
     *   summary="Get User Preferences",
     *   description="Retrieve the user's preferences, including news sources, authors, and categories.",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="User preferences retrieved successfully",
     *     @OA\JsonContent( )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *     )
     *   )
     * )
     */


    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = UserPreference::with('details')->whereUserId($user->id)->first();
        return UserPreferenceResource::collection(collect([$preferences]));
    }

    /**
     * Get Personalized Feed
     * 
     * @OA\Get(
     *   path="/news_project/public/api/personalized-feed",
     *   operationId="getPersonalizedFeed",
     *   tags={"User Preferences"},
     *   summary="Get Personalized Feed",
     *   description="Retrieve a personalized feed of articles based on the user's preferences, including news sources, authors, and categories.",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Personalized feed retrieved successfully",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Preferences not set",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Preferences not set")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *     )
     *   )
     * )
     */


    public function getPersonalizedFeed()
    {
        $user = Auth::user();

        // Fetch user preferences
        $preferences = UserPreferenceDetail::with('user_preference')->whereHas('user_preference',function($q) use($user){
          $q->whereUserId($user->id);  
        })->get();
    
        if ($preferences->isEmpty()) {
            return response()->json(['message' => 'Preferences not set'], 404);
        }
    
        // Extract preferences into separate arrays
        $newsSources = $preferences->pluck('source')->toArray();
        $authors = $preferences->pluck('author')->toArray();
        $category_id = $preferences->pluck('category_id')->toArray();
    
        $feed = Article::where(function ($query) use ($newsSources) {
            foreach ($newsSources as $source) {
                $query->orWhere('source', 'LIKE', "%{$source}%");
            }
        })
        ->where(function ($query) use ($authors) {
            foreach ($authors as $author) {
                $query->orWhere('author', 'LIKE', "%{$author}%");
            }
        })
        ->where(function ($query) use ($category_id) {
            if (!empty($category_id)) {
                $query->whereIn('category_id', $category_id)
                      ->orWhereNull('category_id'); // Include null categories
            } else {
                $query->orWhereNull('category_id'); // Only null categories
            }
        })
        ->get();
    
        return ArticleResource::collection($feed);
    }
}
