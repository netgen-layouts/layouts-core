IF OBJECT_ID('dbo.nglayouts_block_collection', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_block_collection;
IF OBJECT_ID('dbo.nglayouts_collection_item', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_collection_item;
IF OBJECT_ID('dbo.nglayouts_collection_query_translation', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_collection_query_translation;
IF OBJECT_ID('dbo.nglayouts_collection_query', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_collection_query;
IF OBJECT_ID('dbo.nglayouts_collection_slot', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_collection_slot;
IF OBJECT_ID('dbo.nglayouts_collection_translation', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_collection_translation;
IF OBJECT_ID('dbo.nglayouts_collection', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_collection;
IF OBJECT_ID('dbo.nglayouts_zone', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_zone;
IF OBJECT_ID('dbo.nglayouts_block_translation', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_block_translation;
IF OBJECT_ID('dbo.nglayouts_block', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_block;
IF OBJECT_ID('dbo.nglayouts_layout_translation', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_layout_translation;
IF OBJECT_ID('dbo.nglayouts_layout', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_layout;
IF OBJECT_ID('dbo.nglayouts_role_policy', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_role_policy;
IF OBJECT_ID('dbo.nglayouts_role', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_role;
IF OBJECT_ID('dbo.nglayouts_rule_target', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule_target;
IF OBJECT_ID('dbo.nglayouts_rule_condition_rule', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule_condition_rule;
IF OBJECT_ID('dbo.nglayouts_rule_condition_rule_group', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule_condition_rule_group;
IF OBJECT_ID('dbo.nglayouts_rule_condition', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule_condition;
IF OBJECT_ID('dbo.nglayouts_rule_data', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule_data;
IF OBJECT_ID('dbo.nglayouts_rule', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule;
IF OBJECT_ID('dbo.nglayouts_rule_group_data', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule_group_data;
IF OBJECT_ID('dbo.nglayouts_rule_group', 'U') IS NOT NULL DROP TABLE dbo.nglayouts_rule_group;

CREATE TABLE nglayouts_layout (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  type nvarchar(255) NOT NULL,
  name nvarchar(255) NOT NULL,
  description nvarchar(max) NOT NULL,
  created int NOT NULL,
  modified int NOT NULL,
  shared tinyint NOT NULL,
  main_locale nvarchar(255) NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status)
);

CREATE TABLE nglayouts_layout_translation (
  layout_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  PRIMARY KEY (layout_id, status, locale),
  FOREIGN KEY (layout_id, status)
    REFERENCES nglayouts_layout (id, status)
);

CREATE TABLE nglayouts_block (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  layout_id int NOT NULL,
  depth int NOT NULL,
  path nvarchar(255) NOT NULL,
  parent_id int DEFAULT NULL,
  placeholder nvarchar(255) DEFAULT NULL,
  position int DEFAULT NULL,
  definition_identifier nvarchar(255) NOT NULL,
  view_type nvarchar(255) NOT NULL,
  item_view_type nvarchar(255) NOT NULL,
  name nvarchar(255) NOT NULL,
  config nvarchar(max) NOT NULL,
  translatable tinyint NOT NULL,
  main_locale nvarchar(255) NOT NULL,
  always_available tinyint NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status),
  FOREIGN KEY (layout_id, status)
    REFERENCES nglayouts_layout (id, status)
);

CREATE TABLE nglayouts_block_translation (
  block_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  parameters nvarchar(max) NOT NULL,
  PRIMARY KEY (block_id, status, locale),
  FOREIGN KEY (block_id, status)
    REFERENCES nglayouts_block (id, status)
);

CREATE TABLE nglayouts_zone (
  identifier nvarchar(255) NOT NULL,
  layout_id int NOT NULL,
  status int NOT NULL,
  root_block_id int NOT NULL,
  linked_layout_uuid nchar(36),
  linked_zone_identifier nvarchar(255),
  PRIMARY KEY (identifier, layout_id, status),
  FOREIGN KEY (layout_id, status)
    REFERENCES nglayouts_layout (id, status),
  FOREIGN KEY (root_block_id, status)
    REFERENCES nglayouts_block (id, status)
);

CREATE TABLE nglayouts_collection (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  start int NOT NULL,
  length int,
  translatable tinyint NOT NULL,
  main_locale nvarchar(255) NOT NULL,
  always_available tinyint NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status)
);

CREATE TABLE nglayouts_collection_translation (
  collection_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  PRIMARY KEY (collection_id, status, locale),
  FOREIGN KEY (collection_id, status)
    REFERENCES nglayouts_collection (id, status)
);

CREATE TABLE nglayouts_collection_item (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  collection_id int NOT NULL,
  position int NOT NULL,
  value nvarchar(255),
  value_type nvarchar(255) NOT NULL,
  view_type nvarchar(255),
  config nvarchar(max) NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status),
  FOREIGN KEY (collection_id, status)
    REFERENCES nglayouts_collection (id, status)
);

CREATE TABLE nglayouts_collection_query (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  collection_id int NOT NULL,
  type nvarchar(255) NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status),
  FOREIGN KEY (collection_id, status)
    REFERENCES nglayouts_collection (id, status)
);

CREATE TABLE nglayouts_collection_query_translation (
  query_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  parameters nvarchar(max) NOT NULL,
  PRIMARY KEY (query_id, status, locale),
  FOREIGN KEY (query_id, status)
    REFERENCES nglayouts_collection_query (id, status)
);

CREATE TABLE nglayouts_collection_slot (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  collection_id int NOT NULL,
  position int NOT NULL,
  view_type nvarchar(255),
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status),
  FOREIGN KEY (collection_id, status)
    REFERENCES nglayouts_collection (id, status)
);

CREATE TABLE nglayouts_block_collection (
  block_id int NOT NULL,
  block_status int NOT NULL,
  collection_id int NOT NULL,
  collection_status int NOT NULL,
  identifier nvarchar(255) NOT NULL,
  PRIMARY KEY (block_id, block_status, identifier),
  FOREIGN KEY (block_id, block_status)
    REFERENCES nglayouts_block (id, status),
  FOREIGN KEY (collection_id, collection_status)
    REFERENCES nglayouts_collection (id, status)
);

CREATE TABLE nglayouts_role (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  name nvarchar(255) NOT NULL,
  identifier nvarchar(255) NOT NULL,
  description nvarchar(max) NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status)
);

CREATE TABLE nglayouts_role_policy (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  role_id int NOT NULL,
  component nvarchar(255) DEFAULT NULL,
  permission nvarchar(255) DEFAULT NULL,
  limitations nvarchar(max) NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status),
  FOREIGN KEY (role_id, status)
    REFERENCES nglayouts_role (id, status)
);

CREATE TABLE nglayouts_rule (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  rule_group_id int NOT NULL,
  layout_uuid nchar(36) DEFAULT NULL,
  description nvarchar(max) NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status)
);

CREATE TABLE nglayouts_rule_data (
  rule_id int NOT NULL,
  enabled tinyint NOT NULL,
  priority int NOT NULL,
  PRIMARY KEY (rule_id)
);

CREATE TABLE nglayouts_rule_target (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  rule_id int NOT NULL,
  type nvarchar(255) NOT NULL,
  value nvarchar(max),
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status),
  FOREIGN KEY (rule_id, status)
    REFERENCES nglayouts_rule (id, status)
);

CREATE TABLE nglayouts_rule_condition (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  type nvarchar(255) NOT NULL,
  value nvarchar(max),
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status)
);

CREATE TABLE nglayouts_rule_group (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  uuid nchar(36) NOT NULL,
  depth int NOT NULL,
  path nvarchar(255) NOT NULL,
  parent_id int DEFAULT NULL,
  name nvarchar(255) NOT NULL,
  description nvarchar(max) NOT NULL,
  PRIMARY KEY (id, status),
  UNIQUE (uuid, status)
);

CREATE TABLE nglayouts_rule_group_data (
  rule_group_id int NOT NULL,
  enabled tinyint NOT NULL,
  priority int NOT NULL,
  PRIMARY KEY (rule_group_id)
);

CREATE TABLE nglayouts_rule_condition_rule (
  condition_id int NOT NULL,
  condition_status int NOT NULL,
  rule_id int NOT NULL,
  rule_status int NOT NULL,
  PRIMARY KEY (condition_id, condition_status),
  FOREIGN KEY (condition_id, condition_status)
    REFERENCES nglayouts_rule_condition (id, status),
  FOREIGN KEY (rule_id, rule_status)
    REFERENCES nglayouts_rule (id, status)
);

CREATE TABLE nglayouts_rule_condition_rule_group (
  condition_id int NOT NULL,
  condition_status int NOT NULL,
  rule_group_id int NOT NULL,
  rule_group_status int NOT NULL,
  PRIMARY KEY (condition_id, condition_status),
  FOREIGN KEY (condition_id, condition_status)
    REFERENCES nglayouts_rule_condition (id, status),
  FOREIGN KEY (rule_group_id, rule_group_status)
    REFERENCES nglayouts_rule_group (id, status)
);
