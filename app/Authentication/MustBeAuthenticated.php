<?php

declare(strict_types=1);

namespace App\Authentication;

use Attribute;
use Tempest\Router\Route;
use Tempest\Router\RouteDecorator;

#[Attribute]
final readonly class MustBeAuthenticated implements RouteDecorator
{
    public function decorate(Route $route): Route
    {
        $route->middleware[] = EnsureAuthenticated::class;

        return $route;
    }
}
