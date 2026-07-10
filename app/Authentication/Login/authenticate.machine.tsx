import { fetcher } from "@/lib/fetcher";
import { LoginController } from "@gen/routes.gen";
import { router, type UrlMethodPair } from "@inertiajs/core";
import {
  startAuthentication,
  type AuthenticationResponseJSON,
  type PublicKeyCredentialRequestOptionsJSON,
} from "@simplewebauthn/browser";
import { assign, fromPromise, setup } from "xstate";

const performVisit = async (url: UrlMethodPair) => {
  return new Promise((resolve, reject) => {
    router.visit(url, {
      onSuccess: (page) => resolve(page),
      onError: (errors) => reject(errors),
    });
  });
};

export const authenticateMachine = setup({
  types: {
    tags: {} as "pending",
    context: {} as {
      error?: Error;
      redirectTarget?: string;
      authenticationOptions?: PublicKeyCredentialRequestOptionsJSON;
      authenticationResponse?: AuthenticationResponseJSON;
    },
    events: {} as { type: "authenticate" },
  },
  actors: {
    fetchChallenge: fromPromise(async () => {
      return await fetcher<PublicKeyCredentialRequestOptionsJSON>(
        LoginController.authenticationOptions(),
      );
    }),
    authenticatePasskey: fromPromise<
      AuthenticationResponseJSON,
      { optionsJSON: PublicKeyCredentialRequestOptionsJSON }
    >(async ({ input: { optionsJSON } }) => await startAuthentication({ optionsJSON })),
    verifyPasskey: fromPromise<string, { passkey: AuthenticationResponseJSON }>(
      async ({ input }) => {
        const res = await fetcher<{ redirectTo: string }>(LoginController.authenticate(), {
          answer: JSON.stringify(input.passkey),
        });

        return res.redirectTo;
      },
    ),
    redirectAfterSucess: fromPromise<void, { redirectTo: string }>(async ({ input }) => {
      await performVisit({ url: input.redirectTo, method: "get" });
    }),
  },
}).createMachine({
  initial: "idle",
  states: {
    idle: {
      on: {
        authenticate: {
          target: "loadingChallenge",
        },
      },
    },
    loadingChallenge: {
      tags: ["pending"],
      invoke: {
        src: "fetchChallenge",
        onDone: {
          target: "authenticatingPasskey",
          actions: [
            assign({
              authenticationOptions: ({ event }) => event.output,
            }),
          ],
        },
        onError: {
          target: "idle",
          actions: [
            assign({
              error: ({ event }) => event.error as Error,
            }),
          ],
        },
      },
    },
    authenticatingPasskey: {
      tags: ["pending"],
      invoke: {
        src: "authenticatePasskey",
        input: ({ context }) => ({
          optionsJSON: context.authenticationOptions!,
        }),
        onDone: {
          target: "verifyingPasskey",
          actions: [
            assign({
              authenticationResponse: ({ event }) => event.output,
            }),
          ],
        },
        onError: {
          target: "idle",
          actions: [
            assign({
              error: ({ event }) => event.error as Error,
            }),
          ],
        },
      },
    },
    verifyingPasskey: {
      tags: ["pending"],
      invoke: {
        src: "verifyPasskey",
        input: ({ context }) => ({
          passkey: context.authenticationResponse!,
        }),
        onDone: {
          target: "redirecting",
          actions: [
            assign({
              redirectTarget: ({ event }) => event.output,
              authenticationOptions: () => undefined,
              authenticationResponse: () => undefined,
            }),
          ],
        },
        onError: {
          target: "idle",
          actions: [
            assign({
              error: ({ event }) => event.error as Error,
            }),
          ],
        },
      },
    },
    redirecting: {
      tags: ["pending"],
      invoke: {
        src: "redirectAfterSucess",
        input: ({ context }) => ({
          redirectTo: context.redirectTarget!,
        }),
        onDone: {
          target: "idle",
          actions: [
            assign({
              redirectTarget: () => undefined,
            }),
          ],
        },
        onError: {
          target: "idle",
          actions: [
            assign({
              error: ({ event }) => event.error as Error,
            }),
          ],
        },
      },
    },
  },
});
