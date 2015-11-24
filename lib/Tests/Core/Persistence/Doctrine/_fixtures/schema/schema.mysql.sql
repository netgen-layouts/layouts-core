SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `ngbm_layout`;
CREATE TABLE `ngbm_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ngbm_layout_parent_id` (`parent_id`),
  CONSTRAINT `fk_ngbm_layout_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `ngbm_layout` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ngbm_zone`;
CREATE TABLE `ngbm_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layout_id` int(11) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ngbm_zone_layout_id` (`layout_id`),
  CONSTRAINT `fk_ngbm_zone_layout_id` FOREIGN KEY (`layout_id`) REFERENCES `ngbm_layout` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ngbm_block`;
CREATE TABLE `ngbm_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `definition_identifier` varchar(255) NOT NULL,
  `view_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ngbm_block_zone_id` (`zone_id`),
  CONSTRAINT `fk_ngbm_block_zone_id` FOREIGN KEY (`zone_id`) REFERENCES `ngbm_zone` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;
