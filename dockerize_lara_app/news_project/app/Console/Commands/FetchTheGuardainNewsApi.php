<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\UserPreference;
use App\Models\UserPreferenceDetail;
use Illuminate\Console\Command;
use App\Services\GuardianService;
use Carbon\Carbon;

class FetchTheGuardainNewsApi extends Command
{

    protected $guardianService;
    protected $signature = 'app:fetch-guardian-news-api';
    protected $description = 'Command description';

    public function __construct(GuardianService $guardianService)
    {
        parent::__construct(); // Call the parent constructor
        $this->guardianService = $guardianService;
    }

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');

        $userPrefs = UserPreferenceDetail::with('category')->get();
            foreach($userPrefs as $userPref){
                // Fetch articles with filters
                
                    $data = $this->guardianService->fetchArticles([
                        'q' => 'AI',
                        'section' => lcfirst($userPref->category->name),
                        'order-by' => 'newest',
                        'show-fields' => 'byline',
                        'from-date' =>   $yesterday, // Start date
                        'to-date' => $today , 
                    ]);
                if(!empty($data['response']['results'] )){
                    foreach($data['response']['results'] as $item){
                        Article::create([
                            'category_id'=> $userPref->category_id,
                            'title'=> $item['webTitle'] ?? '-',
                            'content'=> $item['webTitle'] ?? '-',
                            'author'=> $item['fields']['byline'] ?? '-',
                            'source'=> $item['source'] ?? 'the guardian news',
                            'published_date'=> $item['webPublicationDate'] ?? '-',
                        ]);
                    }
                    \Log::info('The guardian Fetch successfully');
                    \Log::info($data['response']['results']);
            }
        }
    }
}
