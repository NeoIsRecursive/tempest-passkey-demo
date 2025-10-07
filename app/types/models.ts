export type Datetime = {
  timezone: string;
  timestamp: { seconds: number; nanoseconds: number };
  year: number;
  month: number;
  day: number;
  hours: number;
  minutes: number;
  seconds: number;
  nanoseconds: number;
};

export type User = {
  id: { value: string };
  email: string;
  uuid: string;
  passkeys: Passkey[];
  created_at: Datetime;
  updated_at: Datetime;
};

export type Passkey = {
  id: number;
  credential_id: string;
  user_id: number;
  provider: string | null;
  created_at: string;
  updated_at: string;
};
