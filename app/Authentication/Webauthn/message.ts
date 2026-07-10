import { WebAuthnError } from "@simplewebauthn/browser";

export const friendlyErrMsg = (err: unknown) => {
  if (err instanceof WebAuthnError) {
    return "We couldn't create your passkey. Please try again.";
  }

  if (err instanceof Error) {
    return err.message;
  }

  return "An unknown error occurred";
};
