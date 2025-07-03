CREATE TABLE "public"."users" (
          "id" uuid NOT NULL,
          "pass_hash" varchar(60) NOT NULL,
          "first_name" text,
          "last_name" text,
          "birth_date" date,
          "gender" varchar(10),
          "interests" text,
          "city" varchar(100),
          "created_at" timestamptz DEFAULT now(),
          "updated_at" timestamptz DEFAULT now(),
          "biography" text,
          PRIMARY KEY ("id")
);

-- Indices
CREATE INDEX users_first_name_last_name_idx ON public.users USING btree (first_name, last_name);
