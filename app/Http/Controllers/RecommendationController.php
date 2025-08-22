<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecommendationResource;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function lists()
    {
        $recommendations = Recommendation::with('post')->get();

        return $this->successResponse(RecommendationResource::collection($recommendations), 'Recommendations get succesfully');
    }
    public function store(Request $request)
    {
        $data = $request->validate(['post_id' => ['required', 'exists:posts,id']]);

        $recommendation = Recommendation::create($data);

        return $this->successResponse(new RecommendationResource($recommendation), 'Recommendation get successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $result = Recommendation::destroy($id);
        if (!$result) {
            return $this->errorResponse('Recommendation not found', 404);
        }
        return $this->successResponse(true, 'Recommendation deleted successfully');
    }


}

