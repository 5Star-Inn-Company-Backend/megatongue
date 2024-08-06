<?php
namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class CustomThrottleRequests extends ThrottleRequests
{
    protected function buildResponse($request, $key, $maxAttempts)
    {
        $retryAfter = $this->limiter->availableIn($key);

        $responseData = [
            'status_code' => Response::HTTP_TOO_MANY_REQUESTS,
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
        ];

        return response()->json($responseData, Response::HTTP_TOO_MANY_REQUESTS)
            ->header('Retry-After', $retryAfter);
    }
}

