DROP TABLE IF EXISTS `ngbm_rule_value`;
DROP TABLE IF EXISTS `ngbm_rule_condition`;
DROP TABLE IF EXISTS `ngbm_rule`;

CREATE TABLE `ngbm_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layout_id` int(11) NOT NULL,
  `target_identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ngbm_rule_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`rule_id`)
    REFERENCES `ngbm_rule` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ngbm_rule_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`rule_id`)
    REFERENCES `ngbm_rule` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
