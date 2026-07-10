import { Button } from "@/ui/button";
import { Field, FieldError, FieldLabel } from "@/ui/field";
import { Input } from "@/ui/input";
import { Form, Link } from "@inertiajs/react";
import { CenteredLayout } from "@/centered-layout";
import { LoginController, RegistrationController } from "@gen/routes.gen";

export default function Register() {
  return (
    <CenteredLayout>
      <div className="flex flex-col gap-6 justify-center items-center h-full w-full max-w-xl mx-auto">
        <Form
          className="grid gap-4 w-full"
          action={RegistrationController.create()}
          method="post"
        >
          {({ errors }) => (
            <>
              <Field>
                <FieldLabel>Email</FieldLabel>
                <Input type="email" name="email" required />
                <FieldError errors={errors.email} />
              </Field>

              <Button type="submit">Register</Button>
            </>
          )}
        </Form>
        <p>
          Already have an account?{" "}
          <Link className="text-primary" href={LoginController.view()}>
            Login
          </Link>
        </p>
      </div>
    </CenteredLayout>
  );
}
