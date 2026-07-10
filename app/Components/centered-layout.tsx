import { Link, usePage } from "@inertiajs/react";
import type { PropsWithChildren } from "react";
import { Button } from "./ui/button";
import { DashboardController, DestroySessionController } from "@gen/routes.gen";

export const CenteredLayout = ({ children }: PropsWithChildren) => {
  const { user } = usePage().props;

  return (
    <div className="h-dvh p-2 lg:p-4 pt-0 lg:pt-0 bg-background max-w-3xl mx-auto">
      {user && (
        <nav className="flex justify-between items-center mb-4 py-4 border-b sticky top-0">
          <Link
            href={DashboardController.__invoke()}
            className="text-lg font-bold"
          >
            My app
          </Link>
          <Button
            variant="destructive"
            render={
              <Link
                href={DestroySessionController.__invoke()}
                method="delete"
              />
            }
          >
            Logout
          </Button>
        </nav>
      )}
      <main>{children}</main>
    </div>
  );
};
