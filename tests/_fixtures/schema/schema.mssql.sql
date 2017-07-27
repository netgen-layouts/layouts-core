IF OBJECT_ID('dbo.ngbm_block_collection', 'U') IS NOT NULL DROP TABLE dbo.ngbm_block_collection;
IF OBJECT_ID('dbo.ngbm_collection_item', 'U') IS NOT NULL DROP TABLE dbo.ngbm_collection_item;
IF OBJECT_ID('dbo.ngbm_collection_query_translation', 'U') IS NOT NULL DROP TABLE dbo.ngbm_collection_query_translation;
IF OBJECT_ID('dbo.ngbm_collection_query', 'U') IS NOT NULL DROP TABLE dbo.ngbm_collection_query;
IF OBJECT_ID('dbo.ngbm_collection_translation', 'U') IS NOT NULL DROP TABLE dbo.ngbm_collection_translation;
IF OBJECT_ID('dbo.ngbm_collection', 'U') IS NOT NULL DROP TABLE dbo.ngbm_collection;
IF OBJECT_ID('dbo.ngbm_zone', 'U') IS NOT NULL DROP TABLE dbo.ngbm_zone;
IF OBJECT_ID('dbo.ngbm_block_translation', 'U') IS NOT NULL DROP TABLE dbo.ngbm_block_translation;
IF OBJECT_ID('dbo.ngbm_block', 'U') IS NOT NULL DROP TABLE dbo.ngbm_block;
IF OBJECT_ID('dbo.ngbm_layout_translation', 'U') IS NOT NULL DROP TABLE dbo.ngbm_layout_translation;
IF OBJECT_ID('dbo.ngbm_layout', 'U') IS NOT NULL DROP TABLE dbo.ngbm_layout;
IF OBJECT_ID('dbo.ngbm_rule_target', 'U') IS NOT NULL DROP TABLE dbo.ngbm_rule_target;
IF OBJECT_ID('dbo.ngbm_rule_condition', 'U') IS NOT NULL DROP TABLE dbo.ngbm_rule_condition;
IF OBJECT_ID('dbo.ngbm_rule_data', 'U') IS NOT NULL DROP TABLE dbo.ngbm_rule_data;
IF OBJECT_ID('dbo.ngbm_rule', 'U') IS NOT NULL DROP TABLE dbo.ngbm_rule;

CREATE TABLE ngbm_layout (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  type nvarchar(255) NOT NULL,
  name nvarchar(255) NOT NULL,
  description nvarchar(max) NOT NULL,
  created int NOT NULL,
  modified int NOT NULL,
  shared tinyint NOT NULL,
  main_locale nvarchar(255) NOT NULL,
  PRIMARY KEY (id, status)
);

CREATE TABLE ngbm_layout_translation (
  layout_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  PRIMARY KEY (layout_id, status, locale),
  FOREIGN KEY (layout_id, status)
    REFERENCES ngbm_layout (id, status)
);

CREATE TABLE ngbm_block (
  id int IDENTITY(1, 1),
  status int NOT NULL,
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
  FOREIGN KEY (layout_id, status)
    REFERENCES ngbm_layout (id, status)
);

CREATE TABLE ngbm_block_translation (
  block_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  parameters nvarchar(max) NOT NULL,
  PRIMARY KEY (block_id, status, locale),
  FOREIGN KEY (block_id, status)
    REFERENCES ngbm_block (id, status)
);

CREATE TABLE ngbm_zone (
  identifier nvarchar(255) NOT NULL,
  layout_id int NOT NULL,
  status int NOT NULL,
  root_block_id int NOT NULL,
  linked_layout_id int,
  linked_zone_identifier nvarchar(255),
  PRIMARY KEY (identifier, layout_id, status),
  FOREIGN KEY (layout_id, status)
    REFERENCES ngbm_layout (id, status),
  FOREIGN KEY (root_block_id, status)
    REFERENCES ngbm_block (id, status)
);

CREATE TABLE ngbm_collection (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  translatable tinyint NOT NULL,
  main_locale nvarchar(255) NOT NULL,
  always_available tinyint NOT NULL,
  PRIMARY KEY (id, status)
);

CREATE TABLE ngbm_collection_translation (
  collection_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  PRIMARY KEY (collection_id, status, locale),
  FOREIGN KEY (collection_id, status)
    REFERENCES ngbm_collection (id, status)
);

CREATE TABLE ngbm_collection_item (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  collection_id int NOT NULL,
  position int NOT NULL,
  type int NOT NULL,
  value_id nvarchar(255) NOT NULL,
  value_type nvarchar(255) NOT NULL,
  PRIMARY KEY (id, status),
  FOREIGN KEY (collection_id, status)
    REFERENCES ngbm_collection (id, status)
);

CREATE TABLE ngbm_collection_query (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  collection_id int NOT NULL,
  type nvarchar(255) NOT NULL,
  PRIMARY KEY (id, status),
  FOREIGN KEY (collection_id, status)
    REFERENCES ngbm_collection (id, status)
);

CREATE TABLE ngbm_collection_query_translation (
  query_id int NOT NULL,
  status int NOT NULL,
  locale nvarchar(255) NOT NULL,
  parameters nvarchar(max) NOT NULL,
  PRIMARY KEY (query_id, status, locale),
  FOREIGN KEY (query_id, status)
    REFERENCES ngbm_collection_query (id, status)
);

CREATE TABLE ngbm_block_collection (
  block_id int NOT NULL,
  block_status int NOT NULL,
  collection_id int NOT NULL,
  collection_status int NOT NULL,
  identifier nvarchar(255) NOT NULL,
  start int NOT NULL,
  length int,
  PRIMARY KEY (block_id, block_status, identifier),
  FOREIGN KEY (block_id, block_status)
    REFERENCES ngbm_block (id, status),
  FOREIGN KEY (collection_id, collection_status)
    REFERENCES ngbm_collection (id, status)
);

CREATE TABLE ngbm_rule (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  layout_id int DEFAULT NULL,
  comment nvarchar(255) DEFAULT NULL,
  PRIMARY KEY (id, status)
);

CREATE TABLE ngbm_rule_data (
  rule_id int NOT NULL,
  enabled tinyint NOT NULL,
  priority int NOT NULL,
  PRIMARY KEY (rule_id)
);

CREATE TABLE ngbm_rule_target (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  rule_id int NOT NULL,
  type nvarchar(255) NOT NULL,
  value nvarchar(max) NOT NULL,
  PRIMARY KEY (id, status),
  FOREIGN KEY (rule_id, status)
    REFERENCES ngbm_rule (id, status)
);

CREATE TABLE ngbm_rule_condition (
  id int IDENTITY(1, 1),
  status int NOT NULL,
  rule_id int NOT NULL,
  type nvarchar(255) NOT NULL,
  value nvarchar(max) NOT NULL,
  PRIMARY KEY (id, status),
  FOREIGN KEY (rule_id, status)
    REFERENCES ngbm_rule (id, status)
);
