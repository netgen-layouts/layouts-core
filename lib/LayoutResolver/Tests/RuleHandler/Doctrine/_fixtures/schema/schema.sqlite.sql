DROP TABLE IF EXISTS `ngbm_rule`;
CREATE TABLE `ngbm_rule` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `layout_id` integer NOT NULL,
  `target_identifier` text(255) NOT NULL
);

DROP TABLE IF EXISTS `ngbm_rule_value`;
CREATE TABLE `ngbm_rule_value` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `rule_id` integer NOT NULL,
  `value` text NOT NULL,
  FOREIGN KEY (`rule_id`) REFERENCES `ngbm_rule` (`id`)
);

DROP TABLE IF EXISTS `ngbm_rule_condition`;
CREATE TABLE `ngbm_rule_condition` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `rule_id` integer NOT NULL,
  `matcher` text(255) NOT NULL,
  `value_identifier` text(255) DEFAULT NULL,
  FOREIGN KEY (`rule_id`) REFERENCES `ngbm_rule` (`id`)
);

DROP TABLE IF EXISTS `ngbm_rule_condition_value`;
CREATE TABLE `ngbm_rule_condition_value` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `rule_condition_id` integer NOT NULL,
  `value` text NOT NULL,
  FOREIGN KEY (`rule_condition_id`) REFERENCES `ngbm_rule_condition` (`id`)
);

CREATE INDEX `idx_ngbm_rule_value_rule_id` ON `ngbm_rule_value` ( `rule_id` );
CREATE INDEX `idx_ngbm_rule_condition_rule_id` ON `ngbm_rule_condition` ( `rule_id` );
CREATE INDEX `idx_ngbm_rule_condition_value_rule_condition_id` ON `ngbm_rule_condition_value` ( `rule_condition_id` );
