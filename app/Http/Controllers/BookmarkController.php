<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Store if bookmark doesnt exist and vice verca
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
        ]);

        // Check if the bookmark already exists
        $existingBookmark = Bookmark::where('post_id', $data['post_id'])
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            return $this->successResponse(false, 'Bookmark deleted successfully');
        }

        $data['user_id'] = auth()->user()->id;

        Bookmark::create($data);

        return $this->successResponse(true, 'Bookmark added successfully');
    }
}
