import "@inertiajs/core";

declare module "@inertiajs/core" {
  export interface InertiaConfig {
    sharedPageProps: {
      user: { id: number; email: string } | null;
    };
    errorValueType: string[];
  }
}
