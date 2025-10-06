import { Layout } from "@/components/layout";
import type { PageProps } from "@/types/inertia";
import type { Datetime, Passkey } from "@/types/models";

type Props = PageProps<{
  passkeys: Passkey[];
}>;

export default function Dashboard({ user, passkeys }: Props) {
  if (!user) throw new Error("User not found");

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

  return (
    <Layout>
      <h1 className="text-lg font-bold">Welcome, {user.email}!</h1>
      <p>Your user ID is: {user.id.value}</p>
      <p>Account created: {dateFmt(user.created_at)}</p>

      <h2 className="mt-6 text-md font-semibold">Registered Passkeys:</h2>
      {passkeys.length === 0 ? (
        <p>No passkeys registered.</p>
      ) : (
        <ul className="list-disc">
          {passkeys.map((pk) => (
            <li key={pk.id.value} className="mb-2">
              <p>Credential ID: {pk.credential_id}</p>
              <p>Registered on: {dateFmt(pk.created_at)}</p>
              <p>Last accessed: {dateFmt(pk.updated_at)}</p>
            </li>
          ))}
        </ul>
      )}
    </Layout>
  );
}
