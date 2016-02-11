DROP TABLE IF EXISTS `ngbm_layout`;
CREATE TABLE `ngbm_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  PRIMARY KEY (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ngbm_zone`;
CREATE TABLE `ngbm_zone` (
  `identifier` varchar(255) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`identifier`, `layout_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ngbm_block`;
CREATE TABLE `ngbm_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `zone_identifier` varchar(255) NOT NULL,
  `position` int(11) NOT NULL,
  `definition_identifier` varchar(255) NOT NULL,
  `view_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
