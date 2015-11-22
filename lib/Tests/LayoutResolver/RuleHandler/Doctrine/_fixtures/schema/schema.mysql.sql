SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `ngbm_rule`;
CREATE TABLE `ngbm_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layout_id` int(11) NOT NULL,
  `target_identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ngbm_rule_value`;
CREATE TABLE `ngbm_rule_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ngbm_rule_value_rule_id` (`rule_id`),
  CONSTRAINT `fk_ngbm_rule_value_rule_id` FOREIGN KEY (`rule_id`) REFERENCES `ngbm_rule` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ngbm_rule_condition`;
CREATE TABLE `ngbm_rule_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `matcher` varchar(255) NOT NULL,
  `value_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ngbm_rule_condition_rule_id` (`rule_id`),
  CONSTRAINT `fk_ngbm_rule_condition_rule_id` FOREIGN KEY (`rule_id`) REFERENCES `ngbm_rule` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ngbm_rule_condition_value`;
CREATE TABLE `ngbm_rule_condition_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_condition_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ngbm_rule_condition_value_rule_condition_id` (`rule_condition_id`),
  CONSTRAINT `fk_ngbm_rule_condition_value_rule_condition_id` FOREIGN KEY (`rule_condition_id`) REFERENCES `ngbm_rule_condition` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;
