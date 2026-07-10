<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\Login\LoginController;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Http\Session\SessionManager;
use Tempest\Router\Delete;

use function Tempest\Router\uri;

final readonly class DestroySessionController
{
    #[Delete('/authentication/logout')]
    public function __invoke(Authenticator $authenticator, Session $session, SessionManager $sessionManager): Redirect
    {
        $authenticator->deauthenticate();

        $sessionManager->delete($session);

        return new Redirect(uri([LoginController::class, 'view']));
    }
}
