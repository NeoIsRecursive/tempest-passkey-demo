import type { PageProps } from "@/types/inertia";
import { Form, Link, usePage } from "@inertiajs/react";
import { Button } from "./button";

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
              <Link href="/dashboard">Profile</Link>
            </Button>

            <Form method="POST" action="/auth/logout">
              <Button variant="outline">Logout</Button>
            </Form>
          </div>
        ) : (
          <Button asChild>
            <Link href="/login">Login</Link>
          </Button>
        )}
      </header>
      <main className="grid gap-6 px-6 mx-6">{children}</main>
    </div>
  );
};
