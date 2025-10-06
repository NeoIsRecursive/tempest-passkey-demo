import type { User } from "./models";

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>,
> = {
  user: User | null;
  errors: Record<string, string[]>;
} & T;
