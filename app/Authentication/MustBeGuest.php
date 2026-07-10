<?php

declare(strict_types=1);

namespace App\Authentication;

use Attribute;
use Tempest\Router\Route;
use Tempest\Router\RouteDecorator;

#[Attribute]
final readonly class MustBeGuest implements RouteDecorator
{
    public function decorate(Route $route): Route
    {
        $route->middleware[] = EnsureGuest::class;

        return $route;
    }
}
