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
  id: { value: string };
  credential_id: string;
  public_key: string;
  created_at: Datetime;
  updated_at: Datetime;
};
