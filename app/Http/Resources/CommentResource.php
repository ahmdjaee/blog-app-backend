<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // info('User Login', $request->user());
        return [
            'id' => $this->id,
            'content' => $this->content,
            'post_id' => $this->post_id,
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at->diffForHumans(),
            'likes' => $this->likes->count(),
            'liked' => $this->liked(),
        ];
    }
}
