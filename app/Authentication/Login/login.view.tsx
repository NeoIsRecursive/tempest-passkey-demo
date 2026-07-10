import { Button } from "@/ui/button";
import { Link } from "@inertiajs/react";
import { CenteredLayout } from "@/centered-layout";
import { RegistrationController } from "@gen/routes.gen";
import { InfoIcon, KeyRoundIcon } from "lucide-react";
import { LoginIcon } from "./login-icon";
import { useActor } from "@xstate/react";
import { authenticateMachine } from "./authenticate.machine";
import { Alert, AlertDescription, AlertTitle } from "@/ui/alert";
import { friendlyErrMsg } from "../Webauthn/message";

export default function Login() {
  const [snap, send] = useActor(authenticateMachine);

  return (
    <CenteredLayout>
      <div className="flex flex-col gap-6 justify-center items-center h-full w-full max-w-xl mx-auto">
        <LoginIcon className="size-52" />
        {snap.hasTag("pending") ? (
          <p>pending</p>
        ) : (
          <form
            onSubmit={(e) => {
              e.preventDefault();
              send({ type: "authenticate" });
            }}
            className="grid gap-4 w-full"
          >
            {snap.context.error && (
              <Alert variant="destructive">
                <InfoIcon />
                <AlertTitle>Something went wrong!</AlertTitle>
                <AlertDescription>
                  {friendlyErrMsg(snap.context.error)}
                </AlertDescription>
              </Alert>
            )}
            <Button type="submit">
              <KeyRoundIcon />
              Login with Passkey
            </Button>
          </form>
        )}
        <p className="text-center w-full">
          Don't have an account?{" "}
          <Link href={RegistrationController.view()} className="text-primary">
            Register
          </Link>
        </p>
      </div>
    </CenteredLayout>
  );
}
