<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    protected $withCreatedAt = false;

    public function withCreatedAt()
    {
        $this->withCreatedAt = true;
        return $this;
    }

    public function withToken()
    {
        $this->withToken = true;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'x' => $this->x,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'github' => $this->github,
            'linkedin' => $this->linkedin,
            'short_bio' => $this->short_bio,
            'avatar' => $this->avatar ? url(Storage::url($this->avatar)) : null
        ];

        if ($this->withCreatedAt) {
            $data['created_at'] = $this->created_at->diffForHumans();
        }

        if ($this->withToken) {
            $data['token'] = $request->bearerToken();
        }

        return $data;
    }
}
