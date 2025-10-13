<?php

declare(strict_types=1);

namespace App;

use NeoIsRecursive\Inertia\Http\InertiaResponse;
use Tempest\Router\Get;
use Tempest\View\View;

use function NeoIsRecursive\Inertia\inertia;
use function Tempest\view;

final readonly class HomeController
{
    #[Get('/')]
    public function __invoke(): InertiaResponse
    {
        return inertia('home');
    }
}
