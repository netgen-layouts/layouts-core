CREATE TABLE IF NOT EXISTS "nglayouts_layout" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "type" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "description" text NOT NULL,
  "created" integer NOT NULL,
  "modified" integer NOT NULL,
  "shared" boolean NOT NULL,
  "main_locale" character varying(255) NOT NULL,
  PRIMARY KEY ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_layout_translation" (
  "layout_id" integer NOT NULL,
  "status" integer NOT NULL,
  "locale" character varying(255) NOT NULL,
  PRIMARY KEY ("layout_id", "status", "locale"),
  FOREIGN KEY ("layout_id", "status") REFERENCES nglayouts_layout ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_block" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "layout_id" integer NOT NULL,
  "depth" integer NOT NULL,
  "path" character varying(255) NOT NULL,
  "parent_id" integer,
  "placeholder" character varying(255),
  "position" integer,
  "definition_identifier" character varying(255) NOT NULL,
  "view_type" character varying(255) NOT NULL,
  "item_view_type" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "config" text NOT NULL,
  "translatable" boolean NOT NULL,
  "main_locale" character varying(255) NOT NULL,
  "always_available" boolean NOT NULL,
  PRIMARY KEY ("id", "status"),
  FOREIGN KEY ("layout_id", "status") REFERENCES nglayouts_layout ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_block_translation" (
  "block_id" integer NOT NULL,
  "status" integer NOT NULL,
  "locale" character varying(255) NOT NULL,
  "parameters" text NOT NULL,
  PRIMARY KEY ("block_id", "status", "locale"),
  FOREIGN KEY ("block_id", "status") REFERENCES nglayouts_block ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_zone" (
  "identifier" character varying(255) NOT NULL,
  "layout_id" integer NOT NULL,
  "status" integer NOT NULL,
  "root_block_id" integer NOT NULL,
  "linked_layout_uuid" character(36),
  "linked_zone_identifier" character varying(255),
  PRIMARY KEY ("identifier", "layout_id", "status"),
  FOREIGN KEY ("layout_id", "status") REFERENCES nglayouts_layout ("id", "status"),
  FOREIGN KEY ("root_block_id", "status") REFERENCES nglayouts_block ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_collection" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "start" integer NOT NULL,
  "length" integer,
  "translatable" boolean NOT NULL,
  "main_locale" character varying(255) NOT NULL,
  "always_available" boolean NOT NULL,
  PRIMARY KEY ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_collection_translation" (
  "collection_id" integer NOT NULL,
  "status" integer NOT NULL,
  "locale" character varying(255) NOT NULL,
  PRIMARY KEY ("collection_id", "status", "locale"),
  FOREIGN KEY ("collection_id", "status") REFERENCES nglayouts_collection ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_collection_item" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "collection_id" integer NOT NULL,
  "position" integer NOT NULL,
  "value" character varying(255),
  "value_type" character varying(255) NOT NULL,
  "view_type" character varying(255),
  "config" text NOT NULL,
  PRIMARY KEY ("id", "status"),
  FOREIGN KEY ("collection_id", "status") REFERENCES nglayouts_collection ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_collection_query" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "collection_id" integer NOT NULL,
  "type" character varying(255) NOT NULL,
  PRIMARY KEY ("id", "status"),
  FOREIGN KEY ("collection_id", "status") REFERENCES nglayouts_collection ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_collection_query_translation" (
  "query_id" integer NOT NULL,
  "status" integer NOT NULL,
  "locale" character varying(255) NOT NULL,
  "parameters" text NOT NULL,
  PRIMARY KEY ("query_id", "status", "locale"),
  FOREIGN KEY ("query_id", "status") REFERENCES nglayouts_collection_query ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_collection_slot" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "collection_id" integer NOT NULL,
  "position" integer NOT NULL,
  "view_type" character varying(255),
  PRIMARY KEY ("id", "status"),
  FOREIGN KEY ("collection_id", "status") REFERENCES nglayouts_collection ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_block_collection" (
  "block_id" integer NOT NULL,
  "block_status" integer NOT NULL,
  "collection_id" integer NOT NULL,
  "collection_status" integer NOT NULL,
  "identifier" character varying(255) NOT NULL,
  PRIMARY KEY ("block_id", "block_status", "identifier"),
  FOREIGN KEY ("block_id", "block_status") REFERENCES nglayouts_block ("id", "status"),
  FOREIGN KEY ("collection_id", "collection_status") REFERENCES nglayouts_collection ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_role" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "name" character varying(255) NOT NULL,
  "identifier" character varying(255) NOT NULL,
  "description" text NOT NULL,
  PRIMARY KEY ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_role_policy" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "role_id" integer NOT NULL,
  "component" character varying(255),
  "permission" character varying(255),
  "limitations" text NOT NULL,
  PRIMARY KEY ("id", "status"),
  FOREIGN KEY ("role_id", "status") REFERENCES nglayouts_role ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "rule_group_id" integer NOT NULL,
  "layout_uuid" character(36),
  "description" text NOT NULL,
  PRIMARY KEY ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule_data" (
  "rule_id" integer NOT NULL,
  "enabled" boolean NOT NULL,
  "priority" integer NOT NULL,
  PRIMARY KEY ("rule_id")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule_target" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "rule_id" integer NOT NULL,
  "type" character varying(255) NOT NULL,
  "value" text,
  PRIMARY KEY ("id", "status"),
  FOREIGN KEY ("rule_id", "status") REFERENCES nglayouts_rule ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule_condition" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "type" character varying(255) NOT NULL,
  "value" text,
  PRIMARY KEY ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule_group" (
  "id" integer NOT NULL,
  "status" integer NOT NULL,
  "uuid" character(36) NOT NULL,
  "depth" integer NOT NULL,
  "path" character varying(255) NOT NULL,
  "parent_id" integer,
  "name" character varying(255) NOT NULL,
  "description" text NOT NULL,
  PRIMARY KEY ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule_group_data" (
  "rule_group_id" integer NOT NULL,
  "enabled" boolean NOT NULL,
  "priority" integer NOT NULL,
  PRIMARY KEY ("rule_group_id")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule_condition_rule" (
  "condition_id" integer NOT NULL,
  "condition_status" integer NOT NULL,
  "rule_id" integer NOT NULL,
  "rule_status" integer NOT NULL,
  PRIMARY KEY ("condition_id", "condition_status"),
  FOREIGN KEY ("condition_id", "condition_status") REFERENCES nglayouts_rule_condition ("id", "status"),
  FOREIGN KEY ("rule_id", "rule_status") REFERENCES nglayouts_rule ("id", "status")
);

CREATE TABLE IF NOT EXISTS "nglayouts_rule_condition_rule_group" (
  "condition_id" integer NOT NULL,
  "condition_status" integer NOT NULL,
  "rule_group_id" integer NOT NULL,
  "rule_group_status" integer NOT NULL,
  PRIMARY KEY ("condition_id", "condition_status"),
  FOREIGN KEY ("condition_id", "condition_status") REFERENCES nglayouts_rule_condition ("id", "status"),
  FOREIGN KEY ("rule_group_id", "rule_group_status") REFERENCES nglayouts_rule_group ("id", "status")
);

CREATE SEQUENCE IF NOT EXISTS nglayouts_layout_id_seq;
ALTER SEQUENCE nglayouts_layout_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_layout ALTER COLUMN id SET DEFAULT nextval('nglayouts_layout_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_layout_name ON nglayouts_layout (name);
CREATE INDEX IF NOT EXISTS idx_ngl_layout_type ON nglayouts_layout (type);
CREATE INDEX IF NOT EXISTS idx_ngl_layout_shared ON nglayouts_layout (shared);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_layout_uuid ON nglayouts_layout (uuid, status);

CREATE INDEX IF NOT EXISTS idx_ngl_layout ON nglayouts_zone (layout_id, status);
CREATE INDEX IF NOT EXISTS idx_ngl_root_block ON nglayouts_zone (root_block_id, status);
CREATE INDEX IF NOT EXISTS idx_ngl_linked_zone ON nglayouts_zone (linked_layout_uuid, linked_zone_identifier);

CREATE SEQUENCE IF NOT EXISTS nglayouts_block_id_seq;
ALTER SEQUENCE nglayouts_block_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_block ALTER COLUMN id SET DEFAULT nextval('nglayouts_block_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_layout ON nglayouts_block (layout_id, status);
CREATE INDEX IF NOT EXISTS idx_ngl_parent_block ON nglayouts_block (parent_id, placeholder, status);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_block_uuid ON nglayouts_block (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_collection_id_seq;
ALTER SEQUENCE nglayouts_collection_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_collection ALTER COLUMN id SET DEFAULT nextval('nglayouts_collection_id_seq'::regclass);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_collection_uuid ON nglayouts_collection (uuid, status);

CREATE INDEX IF NOT EXISTS idx_ngl_block ON nglayouts_block_collection (block_id, block_status);
CREATE INDEX IF NOT EXISTS idx_ngl_collection ON nglayouts_block_collection (collection_id, collection_status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_collection_item_id_seq;
ALTER SEQUENCE nglayouts_collection_item_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_collection_item ALTER COLUMN id SET DEFAULT nextval('nglayouts_collection_item_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_collection ON nglayouts_collection_item (collection_id, status);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_collection_item_uuid ON nglayouts_collection_item (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_collection_query_id_seq;
ALTER SEQUENCE nglayouts_collection_query_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_collection_query ALTER COLUMN id SET DEFAULT nextval('nglayouts_collection_query_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_collection ON nglayouts_collection_query (collection_id, status);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_collection_query_uuid ON nglayouts_collection_query (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_collection_slot_id_seq;
ALTER SEQUENCE nglayouts_collection_slot_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_collection_slot ALTER COLUMN id SET DEFAULT nextval('nglayouts_collection_slot_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_collection ON nglayouts_collection_slot (collection_id, status);
CREATE INDEX IF NOT EXISTS idx_ngl_position ON nglayouts_collection_slot (collection_id, position);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_collection_slot_uuid ON nglayouts_collection_slot (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_role_id_seq;
ALTER SEQUENCE nglayouts_role_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_role ALTER COLUMN id SET DEFAULT nextval('nglayouts_role_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_role_identifier ON nglayouts_role (identifier);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_role_uuid ON nglayouts_role (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_role_policy_id_seq;
ALTER SEQUENCE nglayouts_role_policy_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_role_policy ALTER COLUMN id SET DEFAULT nextval('nglayouts_role_policy_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_role ON nglayouts_role_policy (role_id, status);
CREATE INDEX IF NOT EXISTS idx_ngl_policy_component ON nglayouts_role_policy (component);
CREATE INDEX IF NOT EXISTS idx_ngl_policy_component_permission ON nglayouts_role_policy (component, permission);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_role_policy_uuid ON nglayouts_role_policy (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_rule_id_seq;
ALTER SEQUENCE nglayouts_rule_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_rule ALTER COLUMN id SET DEFAULT nextval('nglayouts_rule_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_related_layout ON nglayouts_rule (layout_uuid);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_rule_uuid ON nglayouts_rule (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_rule_target_id_seq;
ALTER SEQUENCE nglayouts_rule_target_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_rule_target ALTER COLUMN id SET DEFAULT nextval('nglayouts_rule_target_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_rule ON nglayouts_rule_target (rule_id, status);
CREATE INDEX IF NOT EXISTS idx_ngl_target_type ON nglayouts_rule_target (type);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_rule_target_uuid ON nglayouts_rule_target (uuid, status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_rule_condition_id_seq;
ALTER SEQUENCE nglayouts_rule_condition_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_rule_condition ALTER COLUMN id SET DEFAULT nextval('nglayouts_rule_condition_id_seq'::regclass);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_rule_condition_uuid ON nglayouts_rule_condition (uuid, status);

CREATE INDEX IF NOT EXISTS idx_ngl_rule ON nglayouts_rule_condition_rule (rule_id, rule_status);

CREATE INDEX IF NOT EXISTS idx_ngl_rule_group ON nglayouts_rule_condition_rule_group (rule_group_id, rule_group_status);

CREATE SEQUENCE IF NOT EXISTS nglayouts_rule_group_id_seq;
ALTER SEQUENCE nglayouts_rule_group_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE ONLY nglayouts_rule_group ALTER COLUMN id SET DEFAULT nextval('nglayouts_rule_group_id_seq'::regclass);
CREATE INDEX IF NOT EXISTS idx_ngl_parent_rule_group ON nglayouts_rule_group (parent_id);
CREATE UNIQUE INDEX IF NOT EXISTS idx_ngl_rule_group_uuid ON nglayouts_rule_group (uuid, status);

INSERT INTO nglayouts_rule_group VALUES (1, 1, '00000000-0000-0000-0000-000000000000', 0, '/1/', NULL, 'Root', '');
INSERT INTO nglayouts_rule_group_data VALUES (1, TRUE, 0);

SELECT setval('nglayouts_layout_id_seq', max(id)) FROM nglayouts_layout;
SELECT setval('nglayouts_block_id_seq', max(id)) FROM nglayouts_block;
SELECT setval('nglayouts_collection_id_seq', max(id)) FROM nglayouts_collection;
SELECT setval('nglayouts_collection_item_id_seq', max(id)) FROM nglayouts_collection_item;
SELECT setval('nglayouts_collection_query_id_seq', max(id)) FROM nglayouts_collection_query;
SELECT setval('nglayouts_collection_slot_id_seq', max(id)) FROM nglayouts_collection_slot;
SELECT setval('nglayouts_role_id_seq', max(id)) FROM nglayouts_role;
SELECT setval('nglayouts_role_policy_id_seq', max(id)) FROM nglayouts_role_policy;
SELECT setval('nglayouts_rule_id_seq', max(id)) FROM nglayouts_rule;
SELECT setval('nglayouts_rule_target_id_seq', max(id)) FROM nglayouts_rule_target;
SELECT setval('nglayouts_rule_condition_id_seq', max(id)) FROM nglayouts_rule_condition;
SELECT setval('nglayouts_rule_group_id_seq', max(id)) FROM nglayouts_rule_group;
