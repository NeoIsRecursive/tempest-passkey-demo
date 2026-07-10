<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\Verification\CodeGenerator;
use Tempest\Http\Session\Session;
use Tempest\Mail\GenericEmail;
use Tempest\Mail\Mailer;

use function Tempest\Database\query;
use function Tempest\DateTime\now;

final readonly class SendVerificationCode
{
    public function __construct(
        private Session $session,
        private Mailer $mailer,
    ) {}

    public function __invoke(string $email, VerificationPurpose $purpose): void
    {
        $code = CodeGenerator::generate();

        $now = now();

        // invalidate any existing codes for this email and purpose
        query(VerificationCode::class)
            ->update(expires_at: $now)
            ->whereField('email', $email)
            ->whereField('purpose', $purpose->value)
            ->execute();

        query(VerificationCode::class)->create(
            email: $email,
            code: $code,
            purpose: $purpose,
            expires_at: $now->plusMinutes(15),
            created_at: $now,
        );

        $this->session->set('email', $email);

        $this->mailer->send(new GenericEmail(subject: 'Verify your mail', to: $email, html: <<<HTML
        <h1>Thanks for joining!</h1>
        <p>Here is your code</p>
        <p>{$code}</p>
        HTML));
    }
}
