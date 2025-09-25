<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

trait ErrorResponseTrait
{
    public function handleException(\Exception $e, string $message, int $status = 404): JsonResponse
    {
        Log::error("{$message} - " . $e->getMessage(), [
            'exception' => $e,
        ]);

        return response()->json([
            'message' => $message,
            'error' => app()->environment('production') || config('app.debug') ? 'Server error' : $e->getMessage()
        ], $status);
    }
}