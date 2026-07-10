import { Link } from "@inertiajs/react";
import { CenteredLayout } from "@/centered-layout";
import { PasskeyController } from "@gen/routes.gen";

export default function Dashboard() {
  return (
    <CenteredLayout>
      <Link href={PasskeyController.index()}>Manage Passkeys</Link>
    </CenteredLayout>
  );
}
