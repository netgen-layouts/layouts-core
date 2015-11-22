DROP TABLE IF EXISTS `ngbm_layout`;
CREATE TABLE `ngbm_layout` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `parent_id` integer DEFAULT NULL,
  `identifier` text(255) NOT NULL,
  `created` integer NOT NULL,
  `modified` integer NOT NULL,
  FOREIGN KEY (`parent_id`) REFERENCES `ngbm_layout` (`id`)
);

DROP TABLE IF EXISTS `ngbm_zone`;
CREATE TABLE `ngbm_zone` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `layout_id` integer NOT NULL,
  `identifier` text(255) NOT NULL,
  FOREIGN KEY (`layout_id`) REFERENCES `ngbm_layout` (`id`)
);

DROP TABLE IF EXISTS `ngbm_block`;
CREATE TABLE `ngbm_block` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `zone_id` integer NOT NULL,
  `definition_identifier` text(255) NOT NULL,
  `view_type` text(255) NOT NULL,
  `parameters` text NOT NULL,
  FOREIGN KEY (`zone_id`) REFERENCES `ngbm_zone` (`id`)
);

CREATE INDEX `idx_ngbm_layout_parent_id` ON `ngbm_layout` ( `parent_id` );
CREATE INDEX `idx_ngbm_zone_layout_id` ON `ngbm_zone` ( `layout_id` );
CREATE INDEX `idx_ngbm_block_zone_id` ON `ngbm_block` ( `zone_id` );
