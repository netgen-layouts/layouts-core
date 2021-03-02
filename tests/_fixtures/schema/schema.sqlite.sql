PRAGMA foreign_keys = ON;
PRAGMA journal_mode = MEMORY;
PRAGMA synchronous = OFF;

DROP TABLE IF EXISTS `nglayouts_block_collection`;
DROP TABLE IF EXISTS `nglayouts_collection_slot`;
DROP TABLE IF EXISTS `nglayouts_collection_item`;
DROP TABLE IF EXISTS `nglayouts_collection_query_translation`;
DROP TABLE IF EXISTS `nglayouts_collection_query`;
DROP TABLE IF EXISTS `nglayouts_collection_translation`;
DROP TABLE IF EXISTS `nglayouts_collection`;
DROP TABLE IF EXISTS `nglayouts_zone`;
DROP TABLE IF EXISTS `nglayouts_block_translation`;
DROP TABLE IF EXISTS `nglayouts_block`;
DROP TABLE IF EXISTS `nglayouts_layout_translation`;
DROP TABLE IF EXISTS `nglayouts_layout`;
DROP TABLE IF EXISTS `nglayouts_role_policy`;
DROP TABLE IF EXISTS `nglayouts_role`;
DROP TABLE IF EXISTS `nglayouts_rule_target`;
DROP TABLE IF EXISTS `nglayouts_rule_condition_rule`;
DROP TABLE IF EXISTS `nglayouts_rule_condition_rule_group`;
DROP TABLE IF EXISTS `nglayouts_rule_condition`;
DROP TABLE IF EXISTS `nglayouts_rule_data`;
DROP TABLE IF EXISTS `nglayouts_rule`;
DROP TABLE IF EXISTS `nglayouts_rule_group_data`;
DROP TABLE IF EXISTS `nglayouts_rule_group`;

CREATE TABLE `nglayouts_layout` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `type` text(255) NOT NULL,
  `name` text(255) NOT NULL,
  `description` longtext NOT NULL,
  `created` integer NOT NULL,
  `modified` integer NOT NULL,
  `shared` integer NOT NULL,
  `main_locale` text(255) NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`)
);

CREATE TABLE `nglayouts_layout_translation` (
  `layout_id` integer NOT NULL,
  `status` integer NOT NULL,
  `locale` text(255) NOT NULL,
  PRIMARY KEY (`layout_id`, `status`, `locale`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES `nglayouts_layout` (`id`, `status`)
);

CREATE TABLE `nglayouts_block` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `layout_id` integer NOT NULL,
  `depth` integer NOT NULL,
  `path` text(255) NOT NULL,
  `parent_id` integer DEFAULT NULL,
  `placeholder` text(255) DEFAULT NULL,
  `position` integer DEFAULT NULL,
  `definition_identifier` text(255) NOT NULL,
  `view_type` text(255) NOT NULL,
  `item_view_type` text(255) NOT NULL,
  `name` text(255) NOT NULL,
  `config` longtext NOT NULL,
  `translatable` integer NOT NULL,
  `main_locale` text(255) NOT NULL,
  `always_available` integer NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES `nglayouts_layout` (`id`, `status`)
);

CREATE TABLE `nglayouts_block_translation` (
  `block_id` integer NOT NULL,
  `status` integer NOT NULL,
  `locale` text(255) NOT NULL,
  `parameters` longtext NOT NULL,
  PRIMARY KEY (`block_id`, `status`, `locale`),
  FOREIGN KEY (`block_id`, `status`)
    REFERENCES `nglayouts_block` (`id`, `status`)
);

CREATE TABLE `nglayouts_zone` (
  `identifier` text(255) NOT NULL,
  `layout_id` integer NOT NULL,
  `status` integer NOT NULL,
  `root_block_id` integer NOT NULL,
  `linked_layout_uuid` text(36),
  `linked_zone_identifier` text(255),
  PRIMARY KEY (`identifier`, `layout_id`, `status`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES `nglayouts_layout` (`id`, `status`),
  FOREIGN KEY (`root_block_id`, `status`)
    REFERENCES `nglayouts_block` (`id`, `status`)
);

CREATE TABLE `nglayouts_collection` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `start` integer NOT NULL,
  `length` integer DEFAULT NULL,
  `translatable` integer NOT NULL,
  `main_locale` text(255) NOT NULL,
  `always_available` integer NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`)
);

CREATE TABLE `nglayouts_collection_translation` (
  `collection_id` integer NOT NULL,
  `status` integer NOT NULL,
  `locale` text(255) NOT NULL,
  PRIMARY KEY (`collection_id`, `status`, `locale`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES `nglayouts_collection` (`id`, `status`)
);

CREATE TABLE `nglayouts_collection_item` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `collection_id` integer NOT NULL,
  `position` integer NOT NULL,
  `value` text(255),
  `value_type` text(255) NOT NULL,
  `view_type` text(255),
  `config` longtext NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES `nglayouts_collection` (`id`, `status`)
);

CREATE TABLE `nglayouts_collection_query` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `collection_id` integer NOT NULL,
  `type` text(255) NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES `nglayouts_collection` (`id`, `status`)
);

CREATE TABLE `nglayouts_collection_query_translation` (
  `query_id` integer NOT NULL,
  `status` integer NOT NULL,
  `locale` text(255) NOT NULL,
  `parameters` longtext NOT NULL,
  PRIMARY KEY (`query_id`, `status`, `locale`),
  FOREIGN KEY (`query_id`, `status`)
    REFERENCES `nglayouts_collection_query` (`id`, `status`)
);

CREATE TABLE `nglayouts_collection_slot` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `collection_id` integer NOT NULL,
  `position` integer NOT NULL,
  `view_type` text(255),
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES `nglayouts_collection` (`id`, `status`)
);

CREATE TABLE `nglayouts_block_collection` (
  `block_id` integer NOT NULL,
  `block_status` integer NOT NULL,
  `collection_id` integer NOT NULL,
  `collection_status` integer NOT NULL,
  `identifier` text(255) NOT NULL,
  PRIMARY KEY (`block_id`, `block_status`, `identifier`),
  FOREIGN KEY (`block_id`, `block_status`)
    REFERENCES `nglayouts_block` (`id`, `status`),
  FOREIGN KEY (`collection_id`, `collection_status`)
    REFERENCES `nglayouts_collection` (`id`, `status`)
);

CREATE TABLE `nglayouts_role_policy` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `role_id` integer NOT NULL,
  `component` text(255) DEFAULT NULL,
  `permission` text(255) DEFAULT NULL,
  `limitations` longtext NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`),
  FOREIGN KEY (`role_id`, `status`)
    REFERENCES `nglayouts_role` (`id`, `status`)
);

CREATE TABLE `nglayouts_role` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `name` text(255) NOT NULL,
  `identifier` text(255) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`)
);

CREATE TABLE `nglayouts_rule_data` (
  `rule_id` integer NOT NULL,
  `enabled` integer NOT NULL,
  `priority` integer NOT NULL,
  PRIMARY KEY (`rule_id`)
);

CREATE TABLE `nglayouts_rule_target` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `rule_id` integer NOT NULL,
  `type` text(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`),
  FOREIGN KEY (`rule_id`, `status`)
    REFERENCES `nglayouts_rule` (`id`, `status`)
);

CREATE TABLE `nglayouts_rule_condition` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `type` text(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`)
);

CREATE TABLE `nglayouts_rule` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `rule_group_id` integer NOT NULL,
  `layout_uuid` text(36) DEFAULT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`)
);

CREATE TABLE `nglayouts_rule_group` (
  `id` integer NOT NULL,
  `status` integer NOT NULL,
  `uuid` text(36) NOT NULL,
  `depth` integer NOT NULL,
  `path` text(255) NOT NULL,
  `parent_id` integer DEFAULT NULL,
  `name` text(255) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE (`uuid`, `status`)
);

CREATE TABLE `nglayouts_rule_group_data` (
  `rule_group_id` integer NOT NULL,
  `enabled` integer NOT NULL,
  `priority` integer NOT NULL,
  PRIMARY KEY (`rule_group_id`)
);

CREATE TABLE `nglayouts_rule_condition_rule` (
  `condition_id` integer NOT NULL,
  `condition_status` integer NOT NULL,
  `rule_id` integer NOT NULL,
  `rule_status` integer NOT NULL,
  PRIMARY KEY (`condition_id`, `condition_status`),
  FOREIGN KEY (`condition_id`, `condition_status`)
    REFERENCES `nglayouts_rule_condition` (`id`, `status`),
  FOREIGN KEY (`rule_id`, `rule_status`)
    REFERENCES `nglayouts_rule` (`id`, `status`)
);

CREATE TABLE `nglayouts_rule_condition_rule_group` (
  `condition_id` integer NOT NULL,
  `condition_status` integer NOT NULL,
  `rule_group_id` integer NOT NULL,
  `rule_group_status` integer NOT NULL,
  PRIMARY KEY (`condition_id`, `condition_status`),
  FOREIGN KEY (`condition_id`, `condition_status`)
    REFERENCES `nglayouts_rule_condition` (`id`, `status`),
  FOREIGN KEY (`rule_group_id`, `rule_group_status`)
    REFERENCES `nglayouts_rule_group` (`id`, `status`)
);
