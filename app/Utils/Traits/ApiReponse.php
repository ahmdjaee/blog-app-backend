<?php

namespace App\Utils\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiReponse
{

    /**
     * API success response
     *
     * @param string $message
     * @param array | JsonResource | bool $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $message, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * API pagination response
     *
     * @param ResourceCollection $collection
     * @param string $message
     * @return ResourceCollection
     */
    public function paginationResponse($collection, $message)
    {
        return $collection->additional([
            'success' => true,
            'message' => $message
        ]);
    }


    /**
     * API error response
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function errorResponse($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code
        ], $code);
    }
}
