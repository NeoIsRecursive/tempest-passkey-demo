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
  id: number;
  email: string;
  uuid: string;
  created_at: string;
  updated_at: string;
};

export type Passkey = {
  id: number;
  credential_id: string;
  user_id: number;
  provider: string | null;
  created_at: string;
  updated_at: string;
};
