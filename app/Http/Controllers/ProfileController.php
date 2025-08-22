<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        return $this->successResponse(new UserResource(auth()->user()), 'Profile get successfully');
    }

    /**
     * Update Profile Account
     */
    public function update(Request $request)
    {
        $user = auth('sanctum')->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'short_bio' => ['nullable', 'string', 'max:160']
        ]);

        $user->update($data);

        return $this->successResponse((new UserResource($user))->withToken(), 'Profile updated successfully');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => ['required', 'image', 'max:5024'],]);
        $user = auth('sanctum')->user();

        if ($request->hasFile('avatar')) {
            $request->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update(['avatar' => $request->avatar]);

        return $this->successResponse((new UserResource($user))->withToken(), 'Avatar updated successfully');
    }

    public function social(Request $request)
    {
        $data = $request->validate([
            'instagram' => ['url', 'nullable'],
            'facebook' => ['url', 'nullable'],
            'x' => ['url', 'nullable'],
            'github' => ['url', 'nullable'],
            'linkedin' => ['url', 'nullable'],
        ]);

        $user = Auth::user();

        $user->update($data);

        return $this->successResponse((new UserResource($user))->withToken(), 'Social updated successfully');
    }


}
