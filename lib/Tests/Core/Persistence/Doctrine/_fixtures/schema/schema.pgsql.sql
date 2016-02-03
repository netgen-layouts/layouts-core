DROP TABLE IF EXISTS "ngbm_layout";
CREATE TABLE "ngbm_layout" (
    "id" integer NOT NULL,
    "parent_id" integer DEFAULT NULL,
    "identifier" varchar(255) NOT NULL,
    "name" varchar(255) NOT NULL,
    "created" integer NOT NULL,
    "modified" integer NOT NULL,
    "status" integer NOT NULL,
    PRIMARY KEY ("id", "status")
);

DROP TABLE IF EXISTS "ngbm_zone";
CREATE TABLE "ngbm_zone" (
    "identifier" varchar(255) NOT NULL,
    "layout_id" integer NOT NULL,
    "status" integer NOT NULL,
    PRIMARY KEY ("identifier", "layout_id", "status")
);

DROP TABLE IF EXISTS "ngbm_block";
CREATE TABLE "ngbm_block" (
    "id" integer NOT NULL,
    "layout_id" integer NOT NULL,
    "zone_identifier" varchar(255) NOT NULL,
    "definition_identifier" varchar(255) NOT NULL,
    "view_type" varchar(255) NOT NULL,
    "name" varchar(255) NOT NULL,
    "parameters" text NOT NULL,
    "status" integer NOT NULL,
    PRIMARY KEY ("id", "status")
);

DROP SEQUENCE IF EXISTS ngbm_layout_id_seq;
CREATE SEQUENCE ngbm_layout_id_seq;
ALTER TABLE "ngbm_layout" ALTER COLUMN "id" SET DEFAULT nextval('ngbm_layout_id_seq');

DROP SEQUENCE IF EXISTS ngbm_block_id_seq;
CREATE SEQUENCE ngbm_block_id_seq;
ALTER TABLE "ngbm_block" ALTER COLUMN "id" SET DEFAULT nextval('ngbm_block_id_seq');
