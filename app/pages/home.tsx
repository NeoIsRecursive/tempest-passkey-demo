import { Layout } from "@/components/layout";

export default function Home() {
  return (
    <Layout>
      <p>
        This is a demo application showcasing the{" "}
        <a className="underline text-blue-400" href="https://tempestphp.com/">
          Tempestphp framework
        </a>{" "}
        and an{" "}
        <a
          className="underline text-blue-400"
          href="https://github.com/NeoIsRecursive/inertia-tempest"
        >
          Inertiajs adapter
        </a>{" "}
        I built for it.
      </p>
      <p>And passkeys, which are really cool.</p>

      <h2 className="text-lg font-semibold">Built with:</h2>
      <ul className="list-disc list-inside">
        <li>Tempestphp</li>
        <li>Inertiajs</li>
        <li>React</li>
        <li>Shadcn components</li>
        <li>TypeScript</li>
        <li>TailwindCSS</li>
        <li>Firehed/webauthn-php (for passkeys)</li>
      </ul>

      <p>
        You can find a link to the repository in the footer if you want to see
        the code.
      </p>
    </Layout>
  );
}
