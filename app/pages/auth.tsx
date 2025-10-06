import { Button } from "@/components/button";
import { Input } from "@/components/input";
import { Layout } from "@/components/layout";
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

    const formData = new FormData(e.currentTarget);
    const email = formData.get("email") as string;

    doLogin(email);
  };

  if (isPending) {
    return (
      <Layout>
        <p>Loading...</p>
      </Layout>
    );
  }

  return (
    <Layout>
      {error && <p className="rounded bg-red-200 p-4 text-red-800">{error}</p>}
      <form onSubmit={handleRegister} className="grid gap-8">
        <Input type="email" name="email" placeholder="Email" required />
        <Button disabled={isPending}>Register with Passkey</Button>
      </form>

      <form onSubmit={handleLogin} className="grid gap-8">
        <Input
          type="email"
          name="email"
          placeholder="Email"
          autoComplete="email webauthn"
          required
        />
        <Button disabled={isPending}>Login</Button>
      </form>
    </Layout>
  );
}
