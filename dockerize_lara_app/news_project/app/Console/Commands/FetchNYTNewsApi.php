<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\UserPreference;
use App\Models\UserPreferenceDetail;
use Illuminate\Console\Command;
use App\Services\NYTService;
use Carbon\Carbon;

class FetchNYTNewsApi extends Command
{

    protected $nytService;
    protected $signature = 'app:fetch-NYT-news-api';
    protected $description = 'Command description';

    public function __construct(NYTService $nytService)
    {
        parent::__construct(); // Call the parent constructor
        $this->nytService = $nytService;
    }

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $userPrefs = UserPreferenceDetail::with('category')->get();
            foreach($userPrefs as $userPref){
                // Fetch articles with filters
                $data = $this->nytService->fetchArticles($userPref->category->name, $today);

                if (isset($data['error'])) {
                    return response()->json($data, 400);
                }
                if(!empty($data['response']['docs'])){
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
                    \Log::info('NYT Article Fetch successfully');
                    \Log::info($data['response']['docs']);
                }else{
                    \Log::info('dont have data to fetch');
                }

               
            }
    }
}
