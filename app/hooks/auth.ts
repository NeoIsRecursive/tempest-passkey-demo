import { login } from "@/lib/webauthn/login";
import { register } from "@/lib/webauthn/register";
import { router } from "@inertiajs/react";
import axios from "axios";
import { useState } from "react";

export const usePasskeyAuth = () => {
  const [isPending, setIsPending] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleError = (error: unknown) => {
    if (!(error instanceof Error)) {
      setError("An unknown error occurred");
    } else if (error.name === "AbortError") {
      setError("Login was aborted. Please try again.");
    } else {
      console.log(axios.isAxiosError(error));
      setError(
        axios.isAxiosError(error)
          ? (error.response?.data?.message ?? error.message)
          : error.message,
      );
    }

    setIsPending(false);
  };

  const doRegister = async (email: string) => {
    setError(null);
    setIsPending(true);
    try {
      const { redirectUri } = await register(email);
      router.visit(redirectUri);
    } catch (error) {
      handleError(error);
    } finally {
      setIsPending(false);
    }
  };

  const doLogin = async (email: string) => {
    setError(null);
    setIsPending(true);
    try {
      const { redirectUri } = await login(email);
      router.visit(redirectUri, {
        onFinish: () => setIsPending(false),
        onError: () => setIsPending(false),
      });
    } catch (error) {
      handleError(error);
    }
  };

  return { doRegister, doLogin, isPending, error };
};
