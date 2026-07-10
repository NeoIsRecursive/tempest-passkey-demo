<?php

declare(strict_types=1);

namespace Passkeys;

use Tempest\Http\Request;
use Tempest\Http\Session\Session;
use Tempest\Validation\Exceptions\ValidationFailed;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\CredentialRecord;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialUserEntity;

use function Tempest\Support\Filesystem\read_json;
use function Tempest\Support\Random\secure_string;

final readonly class PasskeyService
{
    public function __construct(
        private PasskeyConfig $config,
        private Session $session,
    ) {}

    private const string REGISTER_OPTIONS_SESSION_KEY = 'webauthn.register_options';
    private const string AUTHENTICATE_OPTIONS_SESSION_KEY = 'webauthn.request_options';

    public function creationOptions(PublicKeyCredentialUserEntity $user): CredentialCreationOptions
    {
        $options = new PublicKeyCredentialCreationOptions(
            rp: $this->config->relyingParty,
            challenge: secure_string(32),
            user: $user,
            authenticatorSelection: new AuthenticatorSelectionCriteria(
                authenticatorAttachment: AuthenticatorSelectionCriteria::AUTHENTICATOR_ATTACHMENT_NO_PREFERENCE,
                residentKey: AuthenticatorSelectionCriteria::RESIDENT_KEY_REQUIREMENT_REQUIRED,
            ),
        );

        $options = new CredentialCreationOptions($options);

        $this->session->set(self::REGISTER_OPTIONS_SESSION_KEY, $options);

        return $options;
    }

    public function attest(string $passkeyJson, Request $request): CredentialRecord
    {
        $publicKeyCredential = JsonSerializer::deserialize(json: $passkeyJson, into: PublicKeyCredential::class);

        if (! $publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
            throw ValidationFailed::withMessages([
                'passkey' => 'Faulty response type',
            ]);
        }

        $options = $this->session->consume(self::REGISTER_OPTIONS_SESSION_KEY);

        assert($options instanceof CredentialCreationOptions, 'The registration options were not found in the session');

        return AuthenticatorAttestationResponseValidator::create(
            ceremonyStepManager: new CeremonyStepManagerFactory()->creationCeremony(),
        )->check(
            authenticatorAttestationResponse: $publicKeyCredential->response,
            publicKeyCredentialCreationOptions: $options->options,
            host: $request->uri,
        );
    }

    public function authenticateOptions(): CredentialRequestOptions
    {
        $options = new PublicKeyCredentialRequestOptions(
            rpId: $this->config->relyingParty->id,
            challenge: secure_string(32),
            userVerification: PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED,
        );

        $options = new CredentialRequestOptions($options);

        $this->session->set(self::AUTHENTICATE_OPTIONS_SESSION_KEY, $options);

        return $options;
    }

    /**
     * @param callable(string $credentialId): ?CredentialRecord $findPasskeyByCredentialId
     */
    public function authenticate(string $answerJson, callable $findPasskeyByCredentialId): CredentialRecord
    {
        $options = $this->session->consume(self::AUTHENTICATE_OPTIONS_SESSION_KEY);

        assert($options instanceof CredentialRequestOptions, 'The authentication options were not found in the session');

        $publicKeyCredential = JsonSerializer::deserialize($answerJson, PublicKeyCredential::class);

        if (! $publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            throw ValidationFailed::withMessages([
                'answer' => 'The provided passkey was of an invalid type',
            ]);
        }

        $passkey = $findPasskeyByCredentialId($publicKeyCredential->rawId);

        if (! $passkey) {
            throw ValidationFailed::withMessages([
                'answer' => 'The provided passkey was not valid',
            ]);
        }

        return AuthenticatorAssertionResponseValidator::create(
            new CeremonyStepManagerFactory()->requestCeremony(),
        )->check(
            credentialRecord: $passkey,
            authenticatorAssertionResponse: $publicKeyCredential->response,
            publicKeyCredentialRequestOptions: $options->options,
            host: $this->config->relyingParty->id ?? throw new \RuntimeException('Relying party ID is not set'),
            userHandle: null,
        );
    }

    public function getAuthenticatorName(CredentialRecord $credential): ?string
    {
        /**
         * @var array<string, array{name: string}>
         */
        $metamap = read_json(__DIR__ . '/passkeys.json');

        $meta = $metamap[$credential->aaguid->toString()] ?? null;

        if (! $meta) {
            return null;
        }

        return $meta['name'];
    }
}
