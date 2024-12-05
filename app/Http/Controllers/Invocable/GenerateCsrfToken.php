<?php

namespace App\Http\Controllers\Invocable;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GenerateCsrfToken extends controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $sessionToken = $request->session()->token();
        $headerToken = $request->header('X-CSRF-TOKEN');

        if (!$sessionToken || $sessionToken !== $headerToken) {
            return Response::json(['message' => 'Invalid CSRF token'], 403);
        }

        $referer = $request->headers->get('referer');
        $baseAppUrl = config('app.url');

        if (!$referer || !str_starts_with($referer, $baseAppUrl)) {
            return Response::json(['message' => 'Invalid request origin'], 403);
        }

        return Response::json(['csrf_token' => csrf_token()]);
    }
}
