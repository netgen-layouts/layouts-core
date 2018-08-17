CREATE TABLE IF NOT EXISTS `ngbm_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `shared` tinyint NOT NULL,
  `main_locale` varchar(255) NOT NULL,
  PRIMARY KEY (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_layout_translation` (
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY (`layout_id`, `status`, `locale`(191)),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES ngbm_layout (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `definition_identifier` varchar(255) NOT NULL,
  `view_type` varchar(255) NOT NULL,
  `item_view_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(255) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`, `status`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES ngbm_layout (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_block_translation` (
  `block_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`block_id`, `status`, `locale`(191)),
  FOREIGN KEY (`block_id`, `status`)
    REFERENCES ngbm_block (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_zone` (
  `identifier` varchar(255) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `root_block_id` int(11) NOT NULL,
  `linked_layout_id` int(11),
  `linked_zone_identifier` varchar(255),
  PRIMARY KEY (`identifier`(191), `layout_id`, `status`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES ngbm_layout (`id`, `status`),
  FOREIGN KEY (`root_block_id`, `status`)
    REFERENCES ngbm_block (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `length` int(11),
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(255) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_collection_translation` (
  `collection_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY (`collection_id`, `status`, `locale`(191)),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES ngbm_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_collection_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `value` varchar(255),
  `value_type` varchar(255) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES ngbm_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_collection_query` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`, `status`),
  FOREIGN KEY (`collection_id`, `status`)
    REFERENCES ngbm_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_collection_query_translation` (
  `query_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`query_id`, `status`, `locale`(191)),
  FOREIGN KEY (`query_id`, `status`)
    REFERENCES ngbm_collection_query (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_block_collection` (
  `block_id` int(11) NOT NULL,
  `block_status` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `collection_status` int(11) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`block_id`, `block_status`, `identifier`(191)),
  FOREIGN KEY (`block_id`, `block_status`)
    REFERENCES ngbm_block (`id`, `status`),
  FOREIGN KEY (`collection_id`, `collection_status`)
    REFERENCES ngbm_collection (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `layout_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_rule_data` (
  `rule_id` int(11) NOT NULL,
  `enabled` tinyint NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_rule_target` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`, `status`),
  FOREIGN KEY (`rule_id`, `status`)
    REFERENCES `ngbm_rule` (`id`, `status`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ngbm_rule_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`, `status`),
  FOREIGN KEY (`rule_id`, `status`)
    REFERENCES `ngbm_rule` (`id`, `status`)
) ENGINE=InnoDB;

DELETE FROM `ngbm_block_collection`;

DELETE FROM `ngbm_collection_item`;
ALTER TABLE `ngbm_collection_item` AUTO_INCREMENT = 1;

DELETE FROM `ngbm_collection_query_translation`;

DELETE FROM `ngbm_collection_query`;
ALTER TABLE `ngbm_collection_query` AUTO_INCREMENT = 1;

DELETE FROM `ngbm_collection_translation`;

DELETE FROM `ngbm_collection`;
ALTER TABLE `ngbm_collection` AUTO_INCREMENT = 1;

DELETE FROM `ngbm_zone`;

DELETE FROM `ngbm_block_translation`;

DELETE FROM `ngbm_block`;
ALTER TABLE `ngbm_block` AUTO_INCREMENT = 1;

DELETE FROM `ngbm_layout_translation`;

DELETE FROM `ngbm_layout`;
ALTER TABLE `ngbm_layout` AUTO_INCREMENT = 1;

DELETE FROM `ngbm_rule_target`;
ALTER TABLE `ngbm_rule_target` AUTO_INCREMENT = 1;

DELETE FROM `ngbm_rule_condition`;
ALTER TABLE `ngbm_rule_condition` AUTO_INCREMENT = 1;

DELETE FROM `ngbm_rule_data`;

DELETE FROM `ngbm_rule`;
ALTER TABLE `ngbm_rule` AUTO_INCREMENT = 1;
