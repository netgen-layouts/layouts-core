DROP TABLE IF EXISTS "ngbm_block_collection";
DROP TABLE IF EXISTS "ngbm_collection_item";
DROP TABLE IF EXISTS "ngbm_collection_query";
DROP TABLE IF EXISTS "ngbm_collection";
DROP TABLE IF EXISTS "ngbm_block";
DROP TABLE IF EXISTS "ngbm_zone";
DROP TABLE IF EXISTS "ngbm_layout";
DROP TABLE IF EXISTS "ngbm_rule_condition";
DROP TABLE IF EXISTS "ngbm_rule_target";
DROP TABLE IF EXISTS "ngbm_rule_data";
DROP TABLE IF EXISTS "ngbm_rule";

DROP SEQUENCE IF EXISTS ngbm_layout_id_seq;
DROP SEQUENCE IF EXISTS ngbm_block_id_seq;
DROP SEQUENCE IF EXISTS ngbm_collection_id_seq;
DROP SEQUENCE IF EXISTS ngbm_collection_item_id_seq;
DROP SEQUENCE IF EXISTS ngbm_collection_query_id_seq;
DROP SEQUENCE IF EXISTS ngbm_rule_condition_id_seq;
DROP SEQUENCE IF EXISTS ngbm_rule_target_id_seq;
DROP SEQUENCE IF EXISTS ngbm_rule_id_seq;

CREATE TABLE "ngbm_layout" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "type" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "created" integer NOT NULL,
  "modified" integer NOT NULL,
  "shared" boolean NOT NULL
);

CREATE TABLE "ngbm_zone" (
  "identifier" character varying(255) NOT NULL,
  "layout_id" integer NOT NULL,
  "status" integer NOT NULL,
  "linked_layout_id" integer,
  "linked_zone_identifier" character varying(255)
);

CREATE TABLE "ngbm_block" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "layout_id" integer NOT NULL,
  "zone_identifier" character varying(255) NOT NULL,
  "position" integer NOT NULL,
  "definition_identifier" character varying(255) NOT NULL,
  "view_type" character varying(255) NOT NULL,
  "item_view_type" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "parameters" text NOT NULL
);

CREATE TABLE "ngbm_collection" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "type" integer NOT NULL,
  "shared" boolean NOT NULL,
  "name" character varying(255)
);

CREATE TABLE "ngbm_collection_item" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "collection_id" integer NOT NULL,
  "position" integer NOT NULL,
  "type" integer NOT NULL,
  "value_id" character varying(255) NOT NULL,
  "value_type" character varying(255) NOT NULL
);

CREATE TABLE "ngbm_collection_query" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "collection_id" integer NOT NULL,
  "position" integer NOT NULL,
  "identifier" character varying(255) NOT NULL,
  "type" character varying(255) NOT NULL,
  "parameters" text NOT NULL
);

CREATE TABLE "ngbm_block_collection" (
  "block_id" integer NOT NULL,
  "block_status" integer NOT NULL,
  "collection_id" integer NOT NULL,
  "collection_status" integer NOT NULL,
  "identifier" character varying(255) NOT NULL,
  "start" integer NOT NULL,
  "length" integer
);

CREATE TABLE "ngbm_rule" (
    "id" integer NOT NULL,
    "status" integer NOT NULL,
    "layout_id" integer,
    "comment" character varying(255)
);

CREATE TABLE "ngbm_rule_data" (
    "rule_id" integer NOT NULL,
    "enabled" boolean NOT NULL,
    "priority" integer NOT NULL
);

CREATE TABLE "ngbm_rule_target" (
    "id" integer NOT NULL,
    "status" integer NOT NULL,
    "rule_id" integer NOT NULL,
    "type" character varying(255) NOT NULL,
    "value" text
);

CREATE TABLE "ngbm_rule_condition" (
    "id" integer NOT NULL,
    "status" integer NOT NULL,
    "rule_id" integer NOT NULL,
    "type" character varying(255) NOT NULL,
    "value" text
);

CREATE SEQUENCE ngbm_layout_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_layout ALTER COLUMN id SET DEFAULT nextval('ngbm_layout_id_seq'::regclass);

ALTER TABLE ONLY ngbm_layout ADD CONSTRAINT ngbm_layout_pkey PRIMARY KEY ("id", "status");

ALTER TABLE ONLY ngbm_zone ADD CONSTRAINT ngbm_zone_pkey PRIMARY KEY ("identifier", "layout_id", "status");
ALTER TABLE ONLY ngbm_zone ADD FOREIGN KEY ("layout_id", "status") REFERENCES ngbm_layout ("id", "status");

CREATE SEQUENCE ngbm_block_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_block ALTER COLUMN id SET DEFAULT nextval('ngbm_block_id_seq'::regclass);

ALTER TABLE ONLY ngbm_block ADD CONSTRAINT ngbm_block_pkey PRIMARY KEY ("id", "status");
ALTER TABLE ONLY ngbm_block ADD FOREIGN KEY ("layout_id", "status") REFERENCES ngbm_layout ("id", "status");

CREATE SEQUENCE ngbm_collection_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_collection ALTER COLUMN id SET DEFAULT nextval('ngbm_collection_id_seq'::regclass);

ALTER TABLE ONLY ngbm_collection ADD CONSTRAINT ngbm_collection_pkey PRIMARY KEY ("id", "status");

CREATE SEQUENCE ngbm_collection_item_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_collection_item ALTER COLUMN id SET DEFAULT nextval('ngbm_collection_item_id_seq'::regclass);

ALTER TABLE ONLY ngbm_collection_item ADD CONSTRAINT ngbm_collection_item_pkey PRIMARY KEY ("id", "status");
ALTER TABLE ONLY ngbm_collection_item ADD FOREIGN KEY ("collection_id", "status") REFERENCES ngbm_collection ("id", "status");

CREATE SEQUENCE ngbm_collection_query_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_collection_query ALTER COLUMN id SET DEFAULT nextval('ngbm_collection_query_id_seq'::regclass);

ALTER TABLE ONLY ngbm_collection_query ADD CONSTRAINT ngbm_collection_query_pkey PRIMARY KEY ("id", "status");
ALTER TABLE ONLY ngbm_collection_query ADD FOREIGN KEY ("collection_id", "status") REFERENCES ngbm_collection ("id", "status");

ALTER TABLE ONLY ngbm_block_collection ADD CONSTRAINT ngbm_block_collection_pkey PRIMARY KEY ("block_id", "block_status", "identifier");
ALTER TABLE ONLY ngbm_block_collection ADD FOREIGN KEY ("block_id", "block_status") REFERENCES ngbm_block ("id", "status");
ALTER TABLE ONLY ngbm_block_collection ADD FOREIGN KEY ("collection_id", "collection_status") REFERENCES ngbm_collection ("id", "status");

CREATE SEQUENCE ngbm_rule_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_rule ALTER COLUMN id SET DEFAULT nextval('ngbm_rule_id_seq'::regclass);

ALTER TABLE ONLY ngbm_rule ADD CONSTRAINT ngbm_rule_pkey PRIMARY KEY ("id", "status");

ALTER TABLE ONLY ngbm_rule_data ADD CONSTRAINT ngbm_rule_data_pkey PRIMARY KEY ("rule_id");

CREATE SEQUENCE ngbm_rule_target_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_rule_target ALTER COLUMN id SET DEFAULT nextval('ngbm_rule_target_id_seq'::regclass);

ALTER TABLE ONLY ngbm_rule_target ADD CONSTRAINT ngbm_rule_target_pkey PRIMARY KEY ("id", "status");
ALTER TABLE ONLY ngbm_rule_target ADD FOREIGN KEY ("rule_id", "status") REFERENCES ngbm_rule ("id", "status");

CREATE SEQUENCE ngbm_rule_condition_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_rule_condition ALTER COLUMN id SET DEFAULT nextval('ngbm_rule_condition_id_seq'::regclass);

ALTER TABLE ONLY ngbm_rule_condition ADD CONSTRAINT ngbm_rule_condition_pkey PRIMARY KEY ("id", "status");
ALTER TABLE ONLY ngbm_rule_condition ADD FOREIGN KEY ("rule_id", "status") REFERENCES ngbm_rule ("id", "status");
