DROP TABLE IF EXISTS "ngbm_layout";
CREATE TABLE "ngbm_layout" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "parent_id" integer,
  "identifier" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "created" integer NOT NULL,
  "modified" integer NOT NULL
);

DROP TABLE IF EXISTS "ngbm_zone";
CREATE TABLE "ngbm_zone" (
  "identifier" character varying(255) NOT NULL,
  "layout_id" integer NOT NULL,
  "status" integer NOT NULL
);

DROP TABLE IF EXISTS "ngbm_block";
CREATE TABLE "ngbm_block" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "layout_id" integer NOT NULL,
  "zone_identifier" character varying(255) NOT NULL,
  "position" integer NOT NULL,
  "definition_identifier" character varying(255) NOT NULL,
  "view_type" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "parameters" text NOT NULL
);

DROP SEQUENCE IF EXISTS ngbm_layout_id_seq;
CREATE SEQUENCE ngbm_layout_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_layout ALTER COLUMN id SET DEFAULT nextval('ngbm_layout_id_seq'::regclass);
ALTER TABLE ONLY ngbm_layout ADD CONSTRAINT ngbm_layout_pkey PRIMARY KEY ("id", "status");

ALTER TABLE ONLY ngbm_zone ADD CONSTRAINT ngbm_zone_pkey PRIMARY KEY ("identifier", "layout_id", "status");

DROP SEQUENCE IF EXISTS ngbm_block_id_seq;
CREATE SEQUENCE ngbm_block_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_block ALTER COLUMN id SET DEFAULT nextval('ngbm_block_id_seq'::regclass);
ALTER TABLE ONLY ngbm_block ADD CONSTRAINT ngbm_block_pkey PRIMARY KEY ("id", "status");
