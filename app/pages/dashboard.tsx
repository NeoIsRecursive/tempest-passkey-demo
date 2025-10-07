import { Button } from "@/components/button";
import { Layout } from "@/components/layout";
import {
  Item,
  ItemActions,
  ItemContent,
  ItemDescription,
  ItemGroup,
  ItemTitle,
} from "@/components/ui/item";
import { Spinner } from "@/components/ui/spinner";
import { PasskeyController } from "@/Generation/routes.gen";
import { addPasskey } from "@/lib/webauthn/add";
import type { PageProps } from "@/types/inertia";
import type { Datetime, Passkey } from "@/types/models";
import { Form, router } from "@inertiajs/react";
import { KeyRoundIcon } from "lucide-react";
import { useState } from "react";

type Props = PageProps<{
  passkeys: Passkey[];
}>;

export default function Dashboard({ user, passkeys }: Props) {
  const [isPending, setIsPending] = useState(false);

  const handleAddPasskey = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    setIsPending(true);

    try {
      setIsPending(true);
      await addPasskey(user!.email);

      router.reload();
    } catch (error) {
      setIsPending(false);
    }
  };

  return (
    <Layout>
      <h1 className="text-lg font-bold">Welcome, {user!.email}!</h1>
      <p>Your user ID is: {user!.id.value}</p>
      <p>Account created: {dateFmt(user!.created_at)}</p>

      <h2 className="mt-6 text-md font-semibold">Registered Passkeys:</h2>
      {passkeys.length === 0 ? (
        <p>No passkeys registered.</p>
      ) : (
        <ItemGroup className="grid gap-4">
          {passkeys.map((pk) => (
            <Item variant="outline" key={pk.id.value}>
              <ItemContent>
                <ItemTitle>Credential ID: {pk.credential_id}</ItemTitle>
                <ItemDescription>
                  Created: {dateFmt(pk.created_at)}
                  <br />
                  Last used: {dateFmt(pk.updated_at)}
                </ItemDescription>
              </ItemContent>
              <ItemActions>
                <Form
                  action={PasskeyController.remove({
                    id: Number(pk.id.value),
                    someOtherParam: "value",
                  })}
                  onSuccess={() => {
                    console.log("removed", pk);
                  }}
                >
                  <input type="hidden" name="passkey_id" value={pk.id.value} />
                  <Button variant="destructive" size="sm">
                    Delete
                  </Button>
                </Form>
              </ItemActions>
            </Item>
          ))}
        </ItemGroup>
      )}
      <form onSubmit={handleAddPasskey}>
        <Button disabled={isPending}>
          {isPending ? <Spinner /> : <KeyRoundIcon />}
          Add new passkey
        </Button>
      </form>
    </Layout>
  );
}

const dateFmt = (datetime: Datetime) => {
  const date = new Date(
    Date.UTC(
      datetime.year,
      datetime.month - 1, // months are 0-based in JS
      datetime.day,
      datetime.hours,
      datetime.minutes,
      datetime.seconds,
      Math.floor(datetime.nanoseconds / 1e6),
    ),
  );

  return date.toLocaleString();
};
