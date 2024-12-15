<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\UserPreference;
use App\Models\UserPreferenceDetail;
use Illuminate\Http\Request;
use App\Services\NewsAPIService;
use App\Services\NYTService;
use App\Services\GuardianService;

use Carbon\Carbon;
class NewsController extends Controller
{
    protected $newsService;
    protected $nytService;
    protected $guardianService;
    public function __construct(NewsAPIService $newsService,NYTService $nytService,GuardianService $guardianService)
    {
        $this->newsService = $newsService;
        $this->nytService = $nytService;
        $this->guardianService = $guardianService;
    }

    public function index()
    {


        // $data = $this->guardianService->fetchArticles([
        //     'q' => 'AI',
        //     'section' => 'technology',
        //     'order-by' => 'newest',
        //     'show-fields' => 'byline',
        //     'from-date' => '2024-12-13', // Start date
        //     'to-date' => '2024-12-14', 
        // ]);
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');

        $data = $this->guardianService->fetchArticles([
            'q' => 'AI',
            'section' => 'technology',
            'order-by' => 'newest',
            'show-fields' => 'byline',
            'from-date' =>   $yesterday, // Start date
            'to-date' => $today , 
        ]);

        dd($data['response']['results']);











        // $today = Carbon::now()->format('Y-m-d');
        $today = null;
        $userPrefs = UserPreferenceDetail::with('category')->get();
            foreach($userPrefs as $userPref){
                // Fetch articles with filters
                $data = $this->nytService->fetchArticles($userPref->category->name, $today);
                if (isset($data['error'])) {
                    return response()->json($data, 400);
                }

                foreach($data['response']['docs'] as $item){
                    Article::create([
                        'category_id'=> $userPref->category_id,
                        'title'=> $item['snippet'] ?? '-',
                        'content'=> $item['lead_paragraph'] ?? '-',
                        'author'=> $item['byline']['original'] ?? '-',
                        'source'=> $item['source'] ?? '-',
                        'published_date'=> $item['pub_date'] ?? '-',
                    ]);
                }
                \Log::info('New York Times Article Fetch successfully');
            }






 // Define your filters (e.g., search for articles from a specific section, by a certain author, etc.)
        $sectionName = null;
        $source = null;
        $author = null;
        // Fetch articles with filters
        $articles = $this->nytService->fetchArticles($sectionName, $source, $author);

dd($articles['response']['docs']);










        // $preferences = UserPreference::with('details')->whereUserId($user->id)->first();
        // $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');

// dd($yesterday);
        $data = $this->newsService->fetchArticles('News', [
            'language' => 'en',
            'sortBy' => 'publishedAt',
            'from'   => $yesterday,
            // 'from'   => $today,

        ]);

        if (isset($data['error'])) {
            return response()->json($data, 400);
        }
        
dd($data);
        // foreach($data['articles'] as $item){
        //     // dd($item);
        //     Article::create([
        //         'title'=> $item['title'] ?? '-',
        //         'content'=> $item['content'] ?? '-',
        //         'author'=> $item['author'] ?? '-',
        //         'source'=> $item['source']['name'] ?? '-',
        //         'published_date'=> $item['publishedAt'] ?? '-',
        //     ]);
        // }
        \Log::info('Article Fetch successfully');
        
        // return view('news.index', ['articles' => $articles['articles']]);
    }
}
