import type { PageProps } from "@/types/inertia";
import { Form, Link, usePage } from "@inertiajs/react";
import { Button } from "./button";
import { AuthController, DashboardController } from "@/Generation/routes.gen";

export const Layout = ({ children }: { children: React.ReactNode }) => {
  const { user } = usePage<PageProps>().props;
  return (
    <div className="max-w-2xl mx-auto">
      <header className="border-b p-6 m-6 flex justify-between items-center">
        <Link href="/" className="text-2xl font-bold">
          Passkey Tempest
        </Link>

        {user ? (
          <div className="flex gap-4 items-center">
            <Button asChild variant="ghost">
              <Link href={DashboardController.index()}>Profile</Link>
            </Button>

            <Form action={AuthController.logout()}>
              <Button variant="outline">Logout</Button>
            </Form>
          </div>
        ) : (
          <Button asChild>
            <Link href={AuthController.login()}>Login</Link>
          </Button>
        )}
      </header>
      <main className="grid gap-6 px-6 mx-6">{children}</main>
    </div>
  );
};
