DROP TABLE IF EXISTS `ngbm_layout`;
CREATE TABLE `ngbm_layout` (
  `id` integer NOT NULL,
  `parent_id` integer DEFAULT NULL,
  `identifier` text(255) NOT NULL,
  `name` text(255) NOT NULL,
  `created` integer NOT NULL,
  `modified` integer NOT NULL,
  `status` integer NOT NULL,
  PRIMARY KEY (`id`, `status`)
);

DROP TABLE IF EXISTS `ngbm_zone`;
CREATE TABLE `ngbm_zone` (
  `id` integer NOT NULL,
  `layout_id` integer NOT NULL,
  `identifier` text(255) NOT NULL,
  `status` integer NOT NULL,
  PRIMARY KEY (`id`, `status`)
);

DROP TABLE IF EXISTS `ngbm_block`;
CREATE TABLE `ngbm_block` (
  `id` integer NOT NULL,
  `zone_id` integer NOT NULL,
  `definition_identifier` text(255) NOT NULL,
  `view_type` text(255) NOT NULL,
  `name` text(255) NOT NULL,
  `parameters` text NOT NULL,
  `status` integer NOT NULL,
  PRIMARY KEY (`id`, `status`)
);

CREATE INDEX `idx_ngbm_zone_layout_id` ON `ngbm_zone` ( `layout_id` );
CREATE INDEX `idx_ngbm_block_zone_id` ON `ngbm_block` ( `zone_id` );
