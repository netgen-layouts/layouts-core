DROP TABLE IF EXISTS "ngbm_layout";
CREATE TABLE "ngbm_layout" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "parent_id" integer,
  "type" character varying(255) NOT NULL,
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

DROP TABLE IF EXISTS "ngbm_block_collection";
CREATE TABLE "ngbm_block_collection" (
  "block_id" character varying(255) NOT NULL,
  "status" integer NOT NULL,
  "collection_id" integer NOT NULL,
  "identifier" character varying(255) NOT NULL,
  "offset" integer NOT NULL,
  "length" integer
);

DROP TABLE IF EXISTS "ngbm_collection";
CREATE TABLE "ngbm_collection" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "type" integer NOT NULL,
  "name" character varying(255)
);

DROP TABLE IF EXISTS "ngbm_collection_item";
CREATE TABLE "ngbm_collection_item" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "collection_id" integer NOT NULL,
  "position" integer NOT NULL,
  "type" integer NOT NULL,
  "value_id" character varying(255) NOT NULL,
  "value_type" character varying(255) NOT NULL
);

DROP TABLE IF EXISTS "ngbm_collection_query";
CREATE TABLE "ngbm_collection_query" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "collection_id" integer NOT NULL,
  "position" integer NOT NULL,
  "identifier" character varying(255) NOT NULL,
  "type" character varying(255) NOT NULL,
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

ALTER TABLE ONLY ngbm_block_collection ADD CONSTRAINT ngbm_block_collection_pkey PRIMARY KEY ("block_id", "status", "collection_id");

DROP SEQUENCE IF EXISTS ngbm_collection_id_seq;
CREATE SEQUENCE ngbm_collection_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_collection ALTER COLUMN id SET DEFAULT nextval('ngbm_collection_id_seq'::regclass);
ALTER TABLE ONLY ngbm_collection ADD CONSTRAINT ngbm_collection_pkey PRIMARY KEY ("id", "status");

DROP SEQUENCE IF EXISTS ngbm_collection_item_id_seq;
CREATE SEQUENCE ngbm_collection_item_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_collection_item ALTER COLUMN id SET DEFAULT nextval('ngbm_collection_item_id_seq'::regclass);
ALTER TABLE ONLY ngbm_collection_item ADD CONSTRAINT ngbm_collection_item_pkey PRIMARY KEY ("id", "status");

DROP SEQUENCE IF EXISTS ngbm_collection_query_id_seq;
CREATE SEQUENCE ngbm_collection_query_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_collection_query ALTER COLUMN id SET DEFAULT nextval('ngbm_collection_query_id_seq'::regclass);
ALTER TABLE ONLY ngbm_collection_query ADD CONSTRAINT ngbm_collection_query_pkey PRIMARY KEY ("id", "status");
