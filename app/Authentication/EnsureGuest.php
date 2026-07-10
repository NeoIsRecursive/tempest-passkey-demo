<?php

declare(strict_types=1);

namespace App\Authentication;

use App\DashboardController;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Discovery\SkipDiscovery;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;
use Tempest\Support\Priority;

use function Tempest\Router\uri;

#[SkipDiscovery]
#[Priority(Priority::LOW)]
final readonly class EnsureGuest implements HttpMiddleware
{
    public function __construct(
        private Authenticator $authenticator,
    ) {}

    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        $current = $this->authenticator->current();

        if ($current) {
            return new Redirect(uri(DashboardController::class));
        }

        return $next($request);
    }
}
