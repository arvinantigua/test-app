<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Success response.
     *
     * @param array|object $data
     * @param string|boolean $success
     *
     * @return JsonResponse
     */
    public function responseSuccess($data, $success = true): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'data' => $data,
            'error' => false
        ], 200);
    }

    /**
     * Error response.
     *
     * @param string $error
     * @param int $$statusCode
     *
     * @return JsonResponse
     */
    public function responseError($error = "Something went wrong.", $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'error' => $error
        ], $statusCode);
    }
}
