<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($message = 'Success', $data = [], $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'status_code' => $statusCode,
            'data' => $data,
        ], $statusCode);
    }

    public static function error($message = 'An error occurred', $statusCode = 500, $errors = [])
    {
        return response()->json([
            'status' => 'failuer',
            'message' => $message,
            'status_code' => $statusCode,
            'errors' => $errors,
        ], $statusCode);
    }
}
