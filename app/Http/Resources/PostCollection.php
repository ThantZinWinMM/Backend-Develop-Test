<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function index()
    {
        $posts = Post::query()
            ->with(['tags']) // Eager load relationships to reduce query count
            ->select(['id', 'title', 'content', 'created_at', 'author_id']) // Fetch only necessary columns
            ->paginate(10); // Paginate the results for large datasets
        return PostResource::collection($posts);
    }
}
