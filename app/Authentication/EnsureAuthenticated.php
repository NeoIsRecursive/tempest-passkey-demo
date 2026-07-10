<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\Login\LoginController;
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
final readonly class EnsureAuthenticated implements HttpMiddleware
{
    public function __construct(
        private Authenticator $authenticator,
    ) {}

    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        $current = $this->authenticator->current();

        if (! $current) {
            return new Redirect(uri([LoginController::class, 'view']));
        }

        return $next($request);
    }
}
