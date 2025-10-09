import { createInertiaApp } from "@inertiajs/react";
import axios from "axios";
import { createRoot } from "react-dom/client";

axios.defaults.xsrfHeaderName = "X-XSRF-TOKEN";
axios.defaults.xsrfCookieName = "xsrf-token";

createInertiaApp({
  title: (title) => `${title ?? "Passkeys"} - Demo App`,
  resolve: (name) => {
    const pages = import.meta.glob("./pages/**/*.tsx", {
      eager: true,
    });

    return pages[`./pages/${name}.tsx`];
  },
  setup: ({ el, App, props }) => createRoot(el).render(<App {...props} />),
});
