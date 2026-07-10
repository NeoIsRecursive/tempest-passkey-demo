export class FetcherError extends Error {
  name = "FetcherError";

  validationErrors: Record<string, string[]>;

  constructor(message: string, validationErrors: Record<string, string[]>) {
    super(message);

    this.validationErrors = validationErrors;
  }
}

export const fetcher = async <T>(
  urlMethodPair: {
    url: string;
    method?: string;
  },
  body?: object | FormData,
): Promise<T> => {
  const res = await fetch(urlMethodPair.url, {
    method: urlMethodPair.method || "GET",
    body: body
      ? body instanceof FormData
        ? body
        : JSON.stringify(body)
      : undefined,
    headers: {
      Accept: "application/json",
    },
  });

  if (!res.ok) {
    const parsed = await res.json();

    throw new FetcherError(parsed.message, parsed.errors ?? {});
  }

  if (!res.headers.get("Content-Type")?.includes("application/json")) {
    return null as unknown as T;
  }

  return res.json();
};
