<?php

declare(strict_types=1);

namespace App\Middleware;

use Exception;
use Tempest\Http\GenericResponse;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Status;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;

use function Tempest\Support\Arr\contains;

final class HandleHttpException implements HttpMiddleware
{
    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        $wantsJson = contains($request->headers->getHeader('Accept')->values, 'application/json');

        if (! $wantsJson) {
            return $next($request);
        }

        try {
            return $next($request);
        } catch (Exception $failure) {
            return new GenericResponse(
                status: Status::BAD_REQUEST,
                body: [
                    'message' => $failure->getMessage(),
                    'code' => $failure->getCode(),
                    'file' => $failure->getFile(),
                    'line' => $failure->getLine(),
                    'trace' => $failure->getTrace(),
                ],
            );
        }
    }
}
