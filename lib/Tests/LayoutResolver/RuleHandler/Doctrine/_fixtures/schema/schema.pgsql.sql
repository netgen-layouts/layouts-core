DROP TABLE IF EXISTS "ngbm_rule";
CREATE TABLE "ngbm_rule" (
    "id" integer NOT NULL,
    "layout_id" integer NOT NULL,
    "target_identifier" varchar(255) NOT NULL,
    PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "ngbm_rule_value";
CREATE TABLE "ngbm_rule_value" (
    "id" integer NOT NULL,
    "rule_id" integer NOT NULL,
    "value" text NOT NULL,
    PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "ngbm_rule_condition";
CREATE TABLE "ngbm_rule_condition" (
    "id" integer NOT NULL,
    "rule_id" integer NOT NULL,
    "identifier" varchar(255) NOT NULL,
    "parameters" text NOT NULL,
    PRIMARY KEY ("id")
);

DROP SEQUENCE IF EXISTS ngbm_rule_id_seq;
CREATE SEQUENCE ngbm_rule_id_seq;
ALTER TABLE "ngbm_rule" ALTER COLUMN "id" SET DEFAULT nextval('ngbm_rule_id_seq');

DROP SEQUENCE IF EXISTS ngbm_rule_value_id_seq;
CREATE SEQUENCE ngbm_rule_value_id_seq;
ALTER TABLE "ngbm_rule_value" ALTER COLUMN "id" SET DEFAULT nextval('ngbm_rule_value_id_seq');

DROP SEQUENCE IF EXISTS ngbm_rule_condition_id_seq;
CREATE SEQUENCE ngbm_rule_condition_id_seq;
ALTER TABLE "ngbm_rule_condition" ALTER COLUMN "id" SET DEFAULT nextval('ngbm_rule_condition_id_seq');
