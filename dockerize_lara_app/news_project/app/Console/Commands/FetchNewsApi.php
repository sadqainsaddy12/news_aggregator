<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\UserPreference;
use App\Models\UserPreferenceDetail;
use Illuminate\Console\Command;
use App\Services\NewsAPIService;
use Carbon\Carbon;

class FetchNewsApi extends Command
{

    protected $newsService;
    protected $signature = 'app:fetch-news-api';
    protected $description = 'Command description';

    public function __construct(NewsAPIService $newsService)
    {
        parent::__construct(); // Call the parent constructor
        $this->newsService = $newsService;
    }

    public function handle()
    {
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');
        $userPrefs = UserPreferenceDetail::with('category')->get();
            foreach($userPrefs as $userPref){
                $data = $this->newsService->fetchArticles($userPref->category->name, [
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'from'   => $yesterday,
                ]);

                if (isset($data['error'])) {
                    return response()->json($data, 400);
                }

                if(!empty($data['articles'])){
                    foreach($data['articles'] as $item){
                        Article::create([
                            'category_id'=> $userPref->category_id,
                            'title'=> $item['title'] ?? '-',
                            'content'=> $item['content'] ?? '-',
                            'author'=> $item['author'] ?? '-',
                            'source'=> $item['source']['name'] ?? '-',
                            'published_date'=> $item['publishedAt'] ?? '-',
                        ]);
                    }
                    \Log::info('Article Fetch successfully');
                    \Log::info($data['articles']);
            }
        }
    }
}
