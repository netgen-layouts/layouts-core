-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: localhost    Database: netgen_layouts
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ngbm_block`
--

DROP TABLE IF EXISTS `ngbm_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_block` (
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
  `translatable` tinyint(4) NOT NULL,
  `main_locale` varchar(255) NOT NULL,
  `always_available` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`,`status`),
  KEY `idx_ngl_layout` (`layout_id`,`status`),
  KEY `idx_ngl_parent_block` (`parent_id`,`placeholder`(191),`status`),
  CONSTRAINT `fk_ngl_block_layout` FOREIGN KEY (`layout_id`, `status`)
    REFERENCES `ngbm_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_block_collection`
--

DROP TABLE IF EXISTS `ngbm_block_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_block_collection` (
  `block_id` int(11) NOT NULL,
  `block_status` int(11) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `collection_status` int(11) NOT NULL,
  PRIMARY KEY (`block_id`,`block_status`,`identifier`(191)),
  KEY `idx_ngl_block` (`block_id`,`block_status`),
  KEY `idx_ngl_collection` (`collection_id`,`collection_status`),
  CONSTRAINT `fk_ngl_block_collection_block` FOREIGN KEY (`block_id`, `block_status`)
    REFERENCES `ngbm_block` (`id`, `status`),
  CONSTRAINT `fk_ngl_block_collection_collection` FOREIGN KEY (`collection_id`, `collection_status`)
    REFERENCES `ngbm_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_block_translation`
--

DROP TABLE IF EXISTS `ngbm_block_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_block_translation` (
  `block_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`block_id`,`status`,`locale`(191)),
  CONSTRAINT `fk_ngl_block_translation_block` FOREIGN KEY (`block_id`, `status`)
    REFERENCES `ngbm_block` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_collection`
--

DROP TABLE IF EXISTS `ngbm_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `length` int(11) DEFAULT NULL,
  `translatable` tinyint(4) NOT NULL,
  `main_locale` varchar(255) NOT NULL,
  `always_available` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_collection_item`
--

DROP TABLE IF EXISTS `ngbm_collection_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_collection_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `value_type` varchar(255) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  CONSTRAINT `fk_ngl_item_collection` FOREIGN KEY (`collection_id`, `status`)
    REFERENCES `ngbm_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_collection_query`
--

DROP TABLE IF EXISTS `ngbm_collection_query`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_collection_query` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  KEY `idx_ngl_collection_identifier` (`collection_id`,`status`),
  CONSTRAINT `fk_ngl_query_collection` FOREIGN KEY (`collection_id`, `status`)
    REFERENCES `ngbm_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_collection_query_translation`
--

DROP TABLE IF EXISTS `ngbm_collection_query_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_collection_query_translation` (
  `query_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`query_id`,`status`,`locale`(191)),
  CONSTRAINT `fk_ngl_query_translation_query` FOREIGN KEY (`query_id`, `status`)
    REFERENCES `ngbm_collection_query` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_collection_translation`
--

DROP TABLE IF EXISTS `ngbm_collection_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_collection_translation` (
  `collection_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY (`collection_id`,`status`,`locale`(191)),
  CONSTRAINT `fk_ngl_collection_translation_collection` FOREIGN KEY (`collection_id`, `status`)
    REFERENCES `ngbm_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_layout`
--

DROP TABLE IF EXISTS `ngbm_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `shared` tinyint(1) NOT NULL,
  `main_locale` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`status`),
  KEY `idx_ngl_layout_name` (`name`(191)),
  KEY `idx_ngl_layout_type` (`type`(191)),
  KEY `idx_ngl_layout_shared` (`shared`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_layout_translation`
--

DROP TABLE IF EXISTS `ngbm_layout_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_layout_translation` (
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY (`layout_id`,`status`,`locale`(191)),
  CONSTRAINT `fk_ngl_layout_translation_layout` FOREIGN KEY (`layout_id`, `status`)
    REFERENCES `ngbm_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_migration_versions`
--

DROP TABLE IF EXISTS `ngbm_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_migration_versions` (
  `version` varchar(255) NOT NULL,
  PRIMARY KEY (`version`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ngbm_migration_versions`
--

LOCK TABLES `ngbm_migration_versions` WRITE;
/*!40000 ALTER TABLE `ngbm_migration_versions` DISABLE KEYS */;
INSERT INTO `ngbm_migration_versions` VALUES ('000700');
INSERT INTO `ngbm_migration_versions` VALUES ('000800');
INSERT INTO `ngbm_migration_versions` VALUES ('000900');
INSERT INTO `ngbm_migration_versions` VALUES ('001000');
INSERT INTO `ngbm_migration_versions` VALUES ('001100');
INSERT INTO `ngbm_migration_versions` VALUES ('001200');
/*!40000 ALTER TABLE `ngbm_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ngbm_rule`
--

DROP TABLE IF EXISTS `ngbm_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `layout_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`status`),
  KEY `idx_ngl_related_layout` (`layout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_rule_condition`
--

DROP TABLE IF EXISTS `ngbm_rule_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_rule_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`,`status`),
  KEY `idx_ngl_rule` (`rule_id`,`status`),
  CONSTRAINT `fk_ngl_condition_rule` FOREIGN KEY (`rule_id`, `status`)
    REFERENCES `ngbm_rule` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_rule_data`
--

DROP TABLE IF EXISTS `ngbm_rule_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_rule_data` (
  `rule_id` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_rule_target`
--

DROP TABLE IF EXISTS `ngbm_rule_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_rule_target` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`,`status`),
  KEY `idx_ngl_rule` (`rule_id`,`status`),
  KEY `idx_ngl_target_type` (`type`(191)),
  CONSTRAINT `fk_ngl_target_rule` FOREIGN KEY (`rule_id`, `status`)
    REFERENCES `ngbm_rule` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ngbm_zone`
--

DROP TABLE IF EXISTS `ngbm_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ngbm_zone` (
  `identifier` varchar(255) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `root_block_id` int(11) NOT NULL,
  `linked_layout_id` int(11) DEFAULT NULL,
  `linked_zone_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`identifier`(191),`layout_id`,`status`),
  KEY `idx_ngl_layout` (`layout_id`,`status`),
  KEY `idx_ngl_root_block` (`root_block_id`,`status`),
  KEY `idx_ngl_linked_zone` (`linked_layout_id`,`linked_zone_identifier`(191)),
  CONSTRAINT `fk_ngl_zone_block` FOREIGN KEY (`root_block_id`, `status`)
    REFERENCES `ngbm_block` (`id`, `status`),
  CONSTRAINT `fk_ngl_zone_layout` FOREIGN KEY (`layout_id`, `status`)
    REFERENCES `ngbm_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-30 12:00:00
