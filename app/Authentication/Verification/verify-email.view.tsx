import { RefreshCwIcon } from "lucide-react";

import { Button } from "@/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/ui/card";
import { Field, FieldError, FieldLabel } from "@/ui/field";
import {
  InputOTP,
  InputOTPGroup,
  InputOTPSeparator,
  InputOTPSlot,
} from "@/ui/input-otp";
import { Form } from "@inertiajs/react";
import { CenteredLayout } from "@/centered-layout";
import { RegistrationController } from "@gen/routes.gen";

export default function InputOTPForm() {
  const email = new URLSearchParams(window.location.search).get("email");

  return (
    <CenteredLayout>
      <Card className="mx-auto max-w-md">
        <CardHeader>
          <CardTitle>Verify your email</CardTitle>
          <CardDescription>
            Enter the verification code we sent to your email address
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Form
            className="mb-6"
            action={RegistrationController.resendVerificationCode()}
            method="post"
          >
            <Button type="submit" variant="outline">
              <RefreshCwIcon />
              Resend Code
            </Button>
          </Form>
          <Form<{ code: string }>
            action={RegistrationController.verify()}
            method="post"
          >
            {({ errors }) => (
              <>
                {email && <input type="hidden" name="email" value={email} />}
                <Field>
                  <FieldLabel htmlFor="otp-verification">
                    Verification code
                  </FieldLabel>
                  <InputOTP
                    maxLength={6}
                    name="code"
                    id="otp-verification"
                    required
                  >
                    <InputOTPGroup className="*:data-[slot=input-otp-slot]:h-12 *:data-[slot=input-otp-slot]:w-11 *:data-[slot=input-otp-slot]:text-xl">
                      <InputOTPSlot index={0} />
                      <InputOTPSlot index={1} />
                      <InputOTPSlot index={2} />
                    </InputOTPGroup>
                    <InputOTPSeparator className="mx-2" />
                    <InputOTPGroup className="*:data-[slot=input-otp-slot]:h-12 *:data-[slot=input-otp-slot]:w-11 *:data-[slot=input-otp-slot]:text-xl">
                      <InputOTPSlot index={3} />
                      <InputOTPSlot index={4} />
                      <InputOTPSlot index={5} />
                    </InputOTPGroup>
                  </InputOTP>
                  <FieldError errors={errors.code} />
                </Field>
                <Field className="mt-12">
                  <Button type="submit" className="w-full">
                    Verify
                  </Button>
                </Field>
              </>
            )}
          </Form>
        </CardContent>
      </Card>
    </CenteredLayout>
  );
}
