<?php

declare(strict_types=1);

namespace App;

use NeoIsRecursive\Inertia\Http\InertiaResponse;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Http\Responses\Forbidden;
use Tempest\Router\Get;

use function NeoIsRecursive\Inertia\inertia;
use function Tempest\Database\query;

final readonly class DashboardController
{
    #[Get('/dashboard')]
    public function index(Authenticator $auth): InertiaResponse|Forbidden
    {
        if (! $auth->current()) {
            return new Forbidden();
        }

        return inertia('dashboard', [
            'message' => 'Welcome to the dashboard!',
            'passkeys' => query(Passkey::class)
                ->find(user_id: $auth->current()->id)
                ->all(),
        ]);
    }
}
