<?php

declare(strict_types=1);

namespace App;

use App\Authentication\MustBeAuthenticated;
use NeoIsRecursive\Inertia\Http\Component;
use Tempest\Router\Get;

final readonly class DashboardController
{
    #[Get('/'), MustBeAuthenticated]
    public function __invoke(): Component
    {
        return new Component('dashboard');
    }
}
