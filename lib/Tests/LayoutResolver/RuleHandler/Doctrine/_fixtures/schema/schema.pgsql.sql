DROP TABLE IF EXISTS "ngbm_rule_condition";
DROP TABLE IF EXISTS "ngbm_rule_value";
DROP TABLE IF EXISTS "ngbm_rule";

DROP SEQUENCE IF EXISTS ngbm_rule_condition_id_seq;
DROP SEQUENCE IF EXISTS ngbm_rule_value_id_seq;
DROP SEQUENCE IF EXISTS ngbm_rule_id_seq;

CREATE TABLE "ngbm_rule" (
    "id" integer NOT NULL,
    "layout_id" integer NOT NULL,
    "target_identifier" character varying(255) NOT NULL
);

CREATE TABLE "ngbm_rule_value" (
    "id" integer NOT NULL,
    "rule_id" integer NOT NULL,
    "value" text NOT NULL
);

CREATE TABLE "ngbm_rule_condition" (
    "id" integer NOT NULL,
    "rule_id" integer NOT NULL,
    "identifier" character varying(255) NOT NULL,
    "parameters" text NOT NULL
);

CREATE SEQUENCE ngbm_rule_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_rule ALTER COLUMN id SET DEFAULT nextval('ngbm_rule_id_seq'::regclass);

ALTER TABLE ONLY ngbm_rule ADD CONSTRAINT ngbm_rule_pkey PRIMARY KEY ("id");

CREATE SEQUENCE ngbm_rule_value_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_rule_value ALTER COLUMN id SET DEFAULT nextval('ngbm_rule_value_id_seq'::regclass);

ALTER TABLE ONLY ngbm_rule_value ADD CONSTRAINT ngbm_rule_value_pkey PRIMARY KEY ("id");
ALTER TABLE ONLY ngbm_rule_value ADD FOREIGN KEY ("rule_id") REFERENCES ngbm_rule ("id");

CREATE SEQUENCE ngbm_rule_condition_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY ngbm_rule_condition ALTER COLUMN id SET DEFAULT nextval('ngbm_rule_condition_id_seq'::regclass);

ALTER TABLE ONLY ngbm_rule_condition ADD CONSTRAINT ngbm_rule_condition_pkey PRIMARY KEY ("id");
ALTER TABLE ONLY ngbm_rule_value ADD FOREIGN KEY ("rule_id") REFERENCES ngbm_rule ("id");
