-- -------------------------------------------------------------
-- TablePlus 6.3.2(586)
--
-- https://tableplus.com/
--
-- Database: study_db
-- Generation Time: 2025-05-11 16:10:49.1840
-- -------------------------------------------------------------


-- This script only contains the table creation statements and does not fully represent the table in the database. Do not use it as a backup.

-- Table Definition
CREATE TABLE "public"."users" (
    "id" uuid NOT NULL,
    "pass_hash" varchar(60) NOT NULL,
    "first_name" varchar(100),
    "last_name" varchar(100),
    "birth_date" date,
    "gender" varchar(10),
    "interests" text,
    "city" varchar(100),
    "created_at" timestamptz DEFAULT now(),
    "updated_at" timestamptz DEFAULT now(),
    "biography" text,
    PRIMARY KEY ("id")
);

