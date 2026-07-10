import { fetcher } from "@/lib/fetcher";
import { PasskeyController } from "@gen/routes.gen";
import {
  startRegistration,
  WebAuthnError,
  type PublicKeyCredentialCreationOptionsJSON,
  type RegistrationResponseJSON,
} from "@simplewebauthn/browser";
import { assign, emit, fromPromise, setup } from "xstate";

export const createPasskeyMachine = setup({
  types: {
    tags: {} as "pending",
    context: {} as {
      error?: Error;
      creationOptions?: PublicKeyCredentialCreationOptionsJSON;
      registrationResponse?: RegistrationResponseJSON;
    },
    events: {} as { type: "create" },
  },
  actors: {
    fetchChallenge: fromPromise(async () => {
      const res = await fetcher<PublicKeyCredentialCreationOptionsJSON>(
        PasskeyController.registerOptions(),
      );

      return res;
    }),
    createPasskey: fromPromise<
      RegistrationResponseJSON,
      { optionsJSON: PublicKeyCredentialCreationOptionsJSON }
    >(
      async ({ input: { optionsJSON } }) =>
        await startRegistration({ optionsJSON }),
    ),
    savePasskey: fromPromise<void, { passkey: RegistrationResponseJSON }>(
      async ({ input: { passkey } }) => {
        await fetcher<null>(PasskeyController.attest(), {
          passkey: JSON.stringify(passkey),
        });
      },
    ),
  },
}).createMachine({
  initial: "idle",
  states: {
    idle: {
      on: {
        create: {
          target: "loadingChallenge",
        },
      },
    },
    loadingChallenge: {
      tags: ["pending"],
      invoke: {
        src: "fetchChallenge",
        onDone: {
          actions: [
            assign({
              creationOptions: ({ event }) => event.output,
            }),
          ],
          target: "creatingPasskey",
        },
        onError: {
          target: "error",
          actions: [
            assign({
              error: ({ event }) => event.error as Error,
            }),
          ],
        },
      },
    },
    creatingPasskey: {
      tags: ["pending"],
      invoke: {
        src: "createPasskey",
        input: ({ context }) => ({
          optionsJSON: context.creationOptions!,
        }),
        onDone: {
          actions: [
            assign({
              registrationResponse: ({ event }) => event.output,
            }),
          ],
          target: "savingPasskey",
        },
        onError: {
          target: "error",
          actions: [
            assign({
              error: ({ event }) => event.error as WebAuthnError,
            }),
          ],
        },
      },
    },
    savingPasskey: {
      tags: ["pending"],
      invoke: {
        src: "savePasskey",
        input: ({ context }) => ({
          passkey: context.registrationResponse!,
        }),
        onDone: {
          target: "success",
        },
        onError: {
          target: "error",
          actions: [
            assign({
              error: ({ event }) => event.error as Error,
            }),
          ],
        },
      },
    },
    error: {
      on: {
        create: {
          target: "loadingChallenge",
        },
      },
    },
    success: {
      always: {
        actions: [emit({ type: "success" })],
      },
      on: {
        create: {
          target: "loadingChallenge",
        },
      },
    },
  },
});
