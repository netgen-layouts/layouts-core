CREATE TABLE IF NOT EXISTS `nglayouts_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `shared` tinyint NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_layout_translation` (
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(191) NOT NULL,
  PRIMARY KEY (`layout_id`, `status`, `locale`(191)),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES nglayouts_layout (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `path` varchar(191) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `placeholder` varchar(191) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `definition_identifier` varchar(191) NOT NULL,
  `view_type` varchar(191) NOT NULL,
  `item_view_type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `config` text NOT NULL,
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES nglayouts_layout (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_block_translation` (
  `block_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(191) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`block_id`, `status`, `locale`(191)),
  FOREIGN KEY (`block_id`, `status`)
    REFERENCES nglayouts_block (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_zone` (
  `identifier` varchar(191) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `root_block_id` int(11) NOT NULL,
  `linked_layout_id` int(11),
  `linked_zone_identifier` varchar(191),
  PRIMARY KEY (`identifier`(191), `layout_id`, `status`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES nglayouts_layout (`id`, `status`),
  FOREIGN KEY (`root_block_id`, `status`)
    REFERENCES nglayouts_block (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `start` int(11) NOT NULL,
  `length` int(11),
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_collection_translation` (
  `collection_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(191) NOT NULL,
  PRIMARY KEY (`collection_id`, `status`, `locale`(191)),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES nglayouts_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_collection_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `value` varchar(191),
  `value_type` varchar(191) NOT NULL,
  `view_type` varchar(191),
  `config` text NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES nglayouts_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_collection_query` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES nglayouts_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_collection_query_translation` (
  `query_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(191) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`query_id`, `status`, `locale`(191)),
  FOREIGN KEY (`query_id`, `status`)
    REFERENCES nglayouts_collection_query (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_collection_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `view_type` varchar(191) NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES nglayouts_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_block_collection` (
  `block_id` int(11) NOT NULL,
  `block_status` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `collection_status` int(11) NOT NULL,
  `identifier` varchar(191) NOT NULL,
  PRIMARY KEY (`block_id`, `block_status`, `identifier`(191)),
  FOREIGN KEY (`block_id`, `block_status`)
    REFERENCES nglayouts_block (`id`, `status`),
  FOREIGN KEY (`collection_id`, `collection_status`)
    REFERENCES nglayouts_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(191) NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_role_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `role_id` int(11) NOT NULL,
  `component` varchar(191) DEFAULT NULL,
  `permission` varchar(191) DEFAULT NULL,
  `limitations` text NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`),
  FOREIGN KEY (`role_id`, `status`)
    REFERENCES nglayouts_role (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `layout_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_rule_data` (
  `rule_id` int(11) NOT NULL,
  `enabled` tinyint NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_rule_target` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`),
  FOREIGN KEY (`rule_id`, `status`)
    REFERENCES nglayouts_rule (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `nglayouts_rule_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`, `status`),
  UNIQUE KEY (`uuid`, `status`),
  FOREIGN KEY (`rule_id`, `status`)
    REFERENCES nglayouts_rule (`id`, `status`)
) ENGINE=InnoDB;

DELETE FROM `nglayouts_block_collection`;

DELETE FROM `nglayouts_collection_slot`;
ALTER TABLE `nglayouts_collection_slot` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_collection_item`;
ALTER TABLE `nglayouts_collection_item` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_collection_query_translation`;

DELETE FROM `nglayouts_collection_query`;
ALTER TABLE `nglayouts_collection_query` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_collection_translation`;

DELETE FROM `nglayouts_collection`;
ALTER TABLE `nglayouts_collection` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_zone`;

DELETE FROM `nglayouts_block_translation`;

DELETE FROM `nglayouts_block`;
ALTER TABLE `nglayouts_block` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_layout_translation`;

DELETE FROM `nglayouts_layout`;
ALTER TABLE `nglayouts_layout` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_role_policy`;
ALTER TABLE `nglayouts_role_policy` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_role`;
ALTER TABLE `nglayouts_role` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_rule_target`;
ALTER TABLE `nglayouts_rule_target` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_rule_condition`;
ALTER TABLE `nglayouts_rule_condition` AUTO_INCREMENT = 1;

DELETE FROM `nglayouts_rule_data`;

DELETE FROM `nglayouts_rule`;
ALTER TABLE `nglayouts_rule` AUTO_INCREMENT = 1;
