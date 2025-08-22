<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $date = Carbon::parse($this->published_at);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'thumbnail' => url(Storage::url($this->thumbnail)),
            'content' => $this->content,
            'slug' => $this->slug,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar ? url(Storage::url($this->user->avatar)) : null,
            ],
            'published' => $this->published === 1 ? true : false,
            'published_at' => $date->diffForHumans(),
            'likes' => $this->likes->count(),
            'liked' => $this->liked(),
            'comments' => $this->comments->count(),
            'marked' => $this->isBookmark(),
        ];
    }
}
