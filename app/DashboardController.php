<?php

declare(strict_types=1);

namespace App;

use App\Authentication\MustBeAuthenticated;
use NeoIsRecursive\Inertia\Http\Component;
use Tempest\Log\Logger;
use Tempest\Router\Get;

final readonly class DashboardController
{
    #[Get('/'), MustBeAuthenticated]
    public function __invoke(Logger $logger): Component
    {
        return new Component('dashboard');
    }
}
