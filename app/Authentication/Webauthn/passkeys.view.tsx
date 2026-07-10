import { CenteredLayout } from "@/centered-layout";
import { router, type SharedPageProps } from "@inertiajs/core";
import { useEffect } from "react";
import { createPasskeyMachine } from "./create-passkey.machine";
import { useActor } from "@xstate/react";
import { Button } from "@/ui/button";
import {
  CircleAlertIcon,
  InfoIcon,
  KeyRoundIcon,
  PlusCircleIcon,
  TrashIcon,
} from "lucide-react";
import {
  Item,
  ItemActions,
  ItemContent,
  ItemDescription,
  ItemGroup,
  ItemMedia,
  ItemTitle,
} from "@/ui/item";
import { Skeleton } from "@/ui/skeleton";

import { Alert, AlertDescription, AlertTitle } from "@/ui/alert";
import passkeyProviders from "../../../packages/passkeys/passkeys.json" with { type: "json" };
import { Form } from "@inertiajs/react";
import { PasskeyController } from "@gen/routes.gen";
import { friendlyErrMsg } from "./message";

type Passkey = {
  id: number;
  name: string;
  aaguid: string;
  credential_id: string;
  updated_at: string;
  created_at: string;
};

type Props = { credentials: Passkey[] } & SharedPageProps;

const formatDate = (date: string) =>
  new Date(date).toLocaleString(undefined, {
    dateStyle: "medium",
    timeStyle: "short",
  });

export default function Passkeys({ credentials }: Props) {
  const [snapshot, send, actorRef] = useActor(createPasskeyMachine);

  console.log(snapshot.context.error);

  useEffect(() => {
    const sub = actorRef.on("success", () => {
      router.reload({ only: ["credentials"] });
    });

    return sub.unsubscribe;
  }, [actorRef]);

  return (
    <CenteredLayout>
      <div className="grid gap-2">
        <h2 className="text-lg font-bold">Security</h2>
        <p>Here you can manage your security keys.</p>

        {snapshot.context.error && (
          <Alert variant="destructive">
            <InfoIcon />
            <AlertTitle>Something went wrong!</AlertTitle>
            <AlertDescription>
              {friendlyErrMsg(snapshot.context.error)}
            </AlertDescription>
          </Alert>
        )}

        <h3 className="text-md font-semibold">Create a passkey</h3>

        {!snapshot.hasTag("pending") ? (
          <form
            onSubmit={(e) => {
              e.preventDefault();

              send({ type: "create" });
            }}
            className="grid gap-2"
          >
            <Button disabled={!snapshot.can({ type: "create" })} type="submit">
              <PlusCircleIcon />
              Add Passkey
            </Button>
          </form>
        ) : (
          <Skeleton className="h-18.75 grid place-items-center">
            creating passkey
          </Skeleton>
        )}

        <div className="grid gap-4">
          <h3 className="text-md font-semibold">Your Passkeys</h3>
          <ItemGroup>
            {credentials.length === 0 ? (
              <Item variant="muted">
                <ItemMedia variant="icon">
                  <CircleAlertIcon />
                </ItemMedia>
                <ItemContent>
                  <ItemTitle>No passkeys found</ItemTitle>
                  <ItemDescription>Please create one above</ItemDescription>
                </ItemContent>
              </Item>
            ) : (
              credentials.map((passkey) => {
                const provider = passkeyProviders[
                  passkey.aaguid as keyof typeof passkeyProviders
                ] ?? {
                  name: "Unknown",
                  icon_light: null,
                  icon_dark: null,
                };
                return (
                  <Item variant="muted" key={passkey.id}>
                    <ItemMedia variant="image">
                      {provider.icon_light ? (
                        <img
                          src={provider.icon_light}
                          alt={provider.name}
                          className="w-6 h-6"
                        />
                      ) : (
                        <KeyRoundIcon className="w-6 h-6" />
                      )}
                    </ItemMedia>
                    <ItemContent>
                      <ItemTitle className="text-base">
                        {passkey.name}
                      </ItemTitle>
                      <ItemDescription className="grid">
                        <span>
                          Last used:{" "}
                          {passkey.updated_at === passkey.created_at
                            ? "Never"
                            : formatDate(passkey.updated_at)}
                        </span>
                        <span>Created: {formatDate(passkey.created_at)}</span>
                      </ItemDescription>
                    </ItemContent>
                    <ItemActions>
                      <Form
                        action={PasskeyController.destroy({
                          id: String(passkey.id),
                        })}
                      >
                        <Button
                          type="submit"
                          size="icon"
                          variant="destructive"
                          aria-label="remove"
                        >
                          <TrashIcon />
                        </Button>
                      </Form>
                    </ItemActions>
                  </Item>
                );
              })
            )}
          </ItemGroup>
        </div>
      </div>
    </CenteredLayout>
  );
}
