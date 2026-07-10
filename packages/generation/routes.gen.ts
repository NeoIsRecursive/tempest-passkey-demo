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
  destroy: (parameters: {id: string; [key:string]: string | number | boolean}) => {
    const url = `/webauthn/passkeys/{id}`;

    return { method: 'delete', url: builder(url, parameters) } as const;
  },
  index: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/webauthn/passkeys`;

    return { method: 'get', url: builder(url, parameters) } as const;
  },
  registerOptions: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/webauthn/creation-options`;

    return { method: 'post', url: builder(url, parameters) } as const;
  },
  attest: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/webauthn/attest`;

    return { method: 'post', url: builder(url, parameters) } as const;
  },
} as const;

export const DashboardController = {
  __invoke: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/`;

    return { method: 'get', url: builder(url, parameters) } as const;
  },
} as const;

export const RegistrationController = {
  view: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/register`;

    return { method: 'get', url: builder(url, parameters) } as const;
  },
  verify: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/authentication/verify`;

    return { method: 'post', url: builder(url, parameters) } as const;
  },
  create: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/authentication/register`;

    return { method: 'post', url: builder(url, parameters) } as const;
  },
  resendVerificationCode: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/authentication/registration/resend-code`;

    return { method: 'post', url: builder(url, parameters) } as const;
  },
} as const;

export const LoginController = {
  view: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/login`;

    return { method: 'get', url: builder(url, parameters) } as const;
  },
  authenticationOptions: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/login/authentication-options`;

    return { method: 'get', url: builder(url, parameters) } as const;
  },
  authenticate: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/login/authenticate`;

    return { method: 'post', url: builder(url, parameters) } as const;
  },
} as const;

export const DestroySessionController = {
  __invoke: (parameters?: {[key:string]: string | number | boolean}) => {
    const url = `/authentication/logout`;

    return { method: 'delete', url: builder(url, parameters) } as const;
  },
} as const;