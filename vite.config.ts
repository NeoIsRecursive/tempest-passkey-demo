import tailwindcss from "@tailwindcss/vite";
import { defineConfig } from "vite";
import tempest from "vite-plugin-tempest";
import react from "@vitejs/plugin-react";
import path from "node:path";

export default defineConfig({
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./app"),
    },
  },
  plugins: [tailwindcss(), react(), tempest()],
});
