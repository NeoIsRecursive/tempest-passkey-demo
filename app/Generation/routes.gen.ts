const builder = (
  url: string,
  parameters?: Record<string, string | boolean | number>,
) => {
  if (!parameters) return url;

  const remainingParams: Record<string, string> = {};

  for (const [key, value] of Object.entries(parameters)) {
    const placeholder = `{${key}}`;

    if (url.includes(placeholder)) {
      url = url.replace(placeholder, encodeURIComponent(String(value)));
    } else {
      remainingParams[key] = String(value);
    }
  }

  const searchParams = new URLSearchParams(remainingParams);
  const queryString = searchParams.toString();

  if (queryString) {
    url += (url.includes("?") ? "&" : "?") + queryString;
  }

  return url;
};

export const PasskeyController = {
  remove: (parameters: {
    id: number;
    [key: string]: string | number | boolean;
  }) => {
    const url = `/auth/passkey/{id}`;

    return { method: "delete", url: builder(url, parameters) } as const;
  },
  addOptions: (parameters?: { [key: string]: string | number | boolean }) => {
    const url = `/auth/passkeys/add-start`;

    return { method: "post", url: builder(url, parameters) } as const;
  },
  addComplete: (parameters?: { [key: string]: string | number | boolean }) => {
    const url = `/auth/passkeys/add-complete`;

    return { method: "post", url: builder(url, parameters) } as const;
  },
} as const;

export const HomeController = {
  __invoke: (parameters?: { [key: string]: string | number | boolean }) => {
    const url = `/`;

    return { method: "get", url: builder(url, parameters) } as const;
  },
} as const;

export const AuthController = {
  login: (parameters?: { [key: string]: string | number | boolean }) => {
    const url = `/login`;

    return { method: "get", url: builder(url, parameters) } as const;
  },
  logout: (parameters?: { [key: string]: string | number | boolean }) => {
    const url = `/auth/logout`;

    return { method: "post", url: builder(url, parameters) } as const;
  },
  registrationOptions: (parameters?: {
    [key: string]: string | number | boolean;
  }) => {
    const url = `/auth/register/options`;

    return { method: "post", url: builder(url, parameters) } as const;
  },
  completeRegistration: (parameters?: {
    [key: string]: string | number | boolean;
  }) => {
    const url = `/auth/register/complete`;

    return { method: "post", url: builder(url, parameters) } as const;
  },
  loginOptions: (parameters?: { [key: string]: string | number | boolean }) => {
    const url = `/auth/login/options`;

    return { method: "post", url: builder(url, parameters) } as const;
  },
  completeLogin: (parameters?: {
    [key: string]: string | number | boolean;
  }) => {
    const url = `/auth/login/complete`;

    return { method: "post", url: builder(url, parameters) } as const;
  },
} as const;

export const DashboardController = {
  index: (parameters?: { [key: string]: string | number | boolean }) => {
    const url = `/dashboard`;

    return { method: "get", url: builder(url, parameters) } as const;
  },
} as const;
