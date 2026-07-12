import { Link } from "@inertiajs/react";
import { CenteredLayout } from "@/centered-layout";
import { PasskeyController } from "@gen/routes.gen";

export default function Dashboard() {
  return (
    <CenteredLayout>
      <article className="grid gap-4">
        <h1 className="text-lg font-bold">Welcome</h1>

        <p>
          This website is mainly for testing my tempest inertia adapter and to
          scratch the passkeys itch I have developed.
        </p>

        <p>
          Some day it might recieve some (hopefully) usefull feature but for now it is sort
          of a boilerplate.
        </p>

        <Link className="underline" href={PasskeyController.index()}>
          Manage Passkeys
        </Link>
      </article>
    </CenteredLayout>
  );
}
