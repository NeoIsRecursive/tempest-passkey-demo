import { Button } from "@/components/button";
import { Input } from "@/components/input";
import { Layout } from "@/components/layout";
import { Spinner } from "@/components/ui/spinner";
import { usePasskeyAuth } from "@/hooks/auth";

export default function Auth() {
  const { isPending, doLogin, doRegister, error } = usePasskeyAuth();

  const handleRegister = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    const formData = new FormData(e.currentTarget);
    const email = formData.get("email") as string;

    doRegister(email);
  };

  const handleLogin = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    doLogin();
  };

  if (isPending) {
    return (
      <Layout>
        <div className="flex pt-8 items-center justify-center">
          <Spinner className="size-24" />
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      {error && <p className="rounded bg-red-200 p-4 text-red-800">{error}</p>}
      <form onSubmit={handleRegister} className="grid gap-8">
        <Input type="email" name="email" placeholder="Email" required />
        <Button size="lg" disabled={isPending}>
          Register with Passkey
        </Button>
      </form>

      <hr />

      <form onSubmit={handleLogin} className="grid gap-8">
        <Button size="lg" disabled={isPending}>
          Login
        </Button>
      </form>
    </Layout>
  );
}
