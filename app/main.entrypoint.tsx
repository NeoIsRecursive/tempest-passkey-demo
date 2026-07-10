import { createInertiaApp, type ResolvedComponent } from "@inertiajs/react";
import { createRoot } from "react-dom/client";

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob<ResolvedComponent>("./**/*.view.tsx");

    const view = `/${name}.view.tsx`;

    const resolver = Object.entries(pages).find(([key]) => key.endsWith(view))?.[1];

    if (!resolver) {
      throw new Error(`View ${name} does not exist`);
    }

    return resolver();
  },
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />);
  },
});
