import axios from "axios";

export const login = async (email: string) => {
  const { data } = await axios.post<{
    challengeB64: string;
    credential_ids: string[];
  }>(
    "/auth/login/options",
    { email },
    {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    },
  );

  // Similar to registration step 2

  // Call the WebAuthn browser API and get the response. This may throw, which you
  // should handle. Example: user cancels or never interacts with the device.
  const credential = await navigator.credentials.get({
    publicKey: {
      challenge: Uint8Array.from(atob(data.challengeB64), (c) =>
        c.charCodeAt(0),
      ),
      allowCredentials: data.credential_ids.map((id) => ({
        id: Uint8Array.from(atob(id), (c) => c.charCodeAt(0)),
        type: "public-key",
      })),
    },
  });

  if (!(credential instanceof PublicKeyCredential)) {
    throw new Error("Credential is not a PublicKeyCredential");
  }

  const credentialResponse =
    credential.response as AuthenticatorAssertionResponse;

  // Format the credential to send to the server. This must match the format
  // handed by the ResponseParser class. The formatting code below can be used
  // without modification.
  const dataForResponseParser = {
    rawId: Array.from(new Uint8Array(credential.rawId)),
    type: credential.type,
    authenticatorData: Array.from(
      new Uint8Array(credentialResponse.authenticatorData),
    ),
    clientDataJSON: Array.from(
      new Uint8Array(credentialResponse.clientDataJSON),
    ),
    signature: Array.from(new Uint8Array(credentialResponse.signature)),
    userHandle: Array.from(new Uint8Array(credentialResponse.userHandle ?? [])),
  };

  const { data: loggedInData } = await axios.post<{
    redirectUri: string;
  }>("/auth/login/complete", dataForResponseParser, {
    headers: {
      Accept: "application/json",
      "Content-type": "application/json",
    },
  });

  return loggedInData;
};
