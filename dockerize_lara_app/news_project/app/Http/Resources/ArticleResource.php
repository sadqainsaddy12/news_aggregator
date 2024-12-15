<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->author,
            'category_id' => $this->category_id,
            'category' => CategoryResource::collection(collect([$this->category])),
            'source' => $this->source,
            'published_date' => $this->published_date,
        ];
    }
    
}
