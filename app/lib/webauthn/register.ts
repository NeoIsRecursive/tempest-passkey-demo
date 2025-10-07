import { AuthController } from "@/Generation/routes.gen";
import axios from "axios";

export const register = async (email: string) => {
  // See https://www.w3.org/TR/webauthn-2/#sctn-sample-registration for a more annotated example

  const { data } = await axios.post(
    AuthController.registrationOptions().url,
    { email },
    {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    },
  );

  const options = PublicKeyCredential.parseCreationOptionsFromJSON(data);

  const credential = await navigator.credentials.create({ publicKey: options });

  if (!(credential instanceof PublicKeyCredential)) {
    throw new Error("Credential is not a PublicKeyCredential");
  }

  const credentialResponse =
    credential.response as AuthenticatorAttestationResponse;

  // Format the credential to send to the server. This must match the format
  // handed by the ResponseParser class. The formatting code below can be used
  // without modification.
  const dataForResponseParser = {
    rawId: Array.from(new Uint8Array(credential.rawId)),
    type: credential.type,
    attestationObject: Array.from(
      new Uint8Array(credentialResponse.attestationObject),
    ),
    clientDataJSON: Array.from(
      new Uint8Array(credentialResponse.clientDataJSON),
    ),
    transports: credentialResponse.getTransports(),
  };

  // Send this to your endpoint - adjust to your needs.

  const { data: registeredData } = await axios.post<{
    redirectUri: string;
  }>(AuthController.completeRegistration().url, dataForResponseParser, {
    headers: {
      Accept: "application/json",
      "Content-type": "application/json",
    },
  });

  return registeredData;
};
