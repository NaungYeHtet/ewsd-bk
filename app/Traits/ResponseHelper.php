<?php

namespace App\Traits;

trait ResponseHelper
{
    protected function responseSuccess(array $data = [], string $message = 'Success', int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    protected function responseError(string $message = 'Internal Server Error', array $data = [], int $code = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $code,
        ], $code);
    }
}
