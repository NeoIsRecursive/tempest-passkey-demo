import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";

createInertiaApp({
  title: (title) => `${title ?? "Passkeys"} - Demo App`,
  progress: {
    color: "var(--color-primary)",
  },
  resolve: async (name) => {
    const pages = import.meta.glob("./pages/**/*.tsx");

    const page = pages[`./pages/${name}.tsx`];

    if (!page) {
      throw new Error(`Unknown page ${name}.tsx`);
    }

    return await page();
  },
  setup: ({ el, App, props }) => createRoot(el).render(<App {...props} />),
});
