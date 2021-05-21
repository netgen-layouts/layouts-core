-- MySQL dump 10.13  Distrib 8.0.25, for Linux (x86_64)
--
-- Host: localhost    Database: netgen_layouts
-- ------------------------------------------------------
-- Server version	8.0.25-0ubuntu0.21.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `nglayouts_block`
--

DROP TABLE IF EXISTS `nglayouts_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_block` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `layout_id` int NOT NULL,
  `depth` int NOT NULL,
  `path` varchar(191) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `placeholder` varchar(191) DEFAULT NULL,
  `position` int DEFAULT NULL,
  `definition_identifier` varchar(191) NOT NULL,
  `view_type` varchar(191) NOT NULL,
  `item_view_type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `config` longtext NOT NULL,
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_block_uuid` (`uuid`,`status`),
  KEY `idx_ngl_layout` (`layout_id`,`status`),
  KEY `idx_ngl_parent_block` (`parent_id`,`placeholder`,`status`),
  CONSTRAINT `fk_ngl_block_layout` FOREIGN KEY (`layout_id`, `status`) REFERENCES `nglayouts_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_block_collection`
--

DROP TABLE IF EXISTS `nglayouts_block_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_block_collection` (
  `block_id` int NOT NULL,
  `block_status` int NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `collection_id` int NOT NULL,
  `collection_status` int NOT NULL,
  PRIMARY KEY (`block_id`,`block_status`,`identifier`),
  KEY `idx_ngl_block` (`block_id`,`block_status`),
  KEY `idx_ngl_collection` (`collection_id`,`collection_status`),
  CONSTRAINT `fk_ngl_block_collection_block` FOREIGN KEY (`block_id`, `block_status`) REFERENCES `nglayouts_block` (`id`, `status`),
  CONSTRAINT `fk_ngl_block_collection_collection` FOREIGN KEY (`collection_id`, `collection_status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_block_translation`
--

DROP TABLE IF EXISTS `nglayouts_block_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_block_translation` (
  `block_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  `parameters` longtext NOT NULL,
  PRIMARY KEY (`block_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_block_translation_block` FOREIGN KEY (`block_id`, `status`) REFERENCES `nglayouts_block` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection`
--

DROP TABLE IF EXISTS `nglayouts_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `start` int NOT NULL,
  `length` int DEFAULT NULL,
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_uuid` (`uuid`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_item`
--

DROP TABLE IF EXISTS `nglayouts_collection_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int NOT NULL,
  `position` int NOT NULL,
  `value` varchar(191) DEFAULT NULL,
  `value_type` varchar(191) NOT NULL,
  `view_type` varchar(191) DEFAULT NULL,
  `config` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_item_uuid` (`uuid`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  CONSTRAINT `fk_ngl_item_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_query`
--

DROP TABLE IF EXISTS `nglayouts_collection_query`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_query` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int NOT NULL,
  `type` varchar(191) NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_query_uuid` (`uuid`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  CONSTRAINT `fk_ngl_query_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_query_translation`
--

DROP TABLE IF EXISTS `nglayouts_collection_query_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_query_translation` (
  `query_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  `parameters` longtext NOT NULL,
  PRIMARY KEY (`query_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_query_translation_query` FOREIGN KEY (`query_id`, `status`) REFERENCES `nglayouts_collection_query` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_slot`
--

DROP TABLE IF EXISTS `nglayouts_collection_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_slot` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int NOT NULL,
  `position` int NOT NULL,
  `view_type` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_slot_uuid` (`uuid`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  KEY `idx_ngl_position` (`collection_id`,`position`),
  CONSTRAINT `fk_ngl_slot_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_translation`
--

DROP TABLE IF EXISTS `nglayouts_collection_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_translation` (
  `collection_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  PRIMARY KEY (`collection_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_collection_translation_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_layout`
--

DROP TABLE IF EXISTS `nglayouts_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_layout` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  `created` int NOT NULL,
  `modified` int NOT NULL,
  `shared` tinyint(1) NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_layout_uuid` (`uuid`,`status`),
  KEY `idx_ngl_layout_name` (`name`),
  KEY `idx_ngl_layout_type` (`type`),
  KEY `idx_ngl_layout_shared` (`shared`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_layout_translation`
--

DROP TABLE IF EXISTS `nglayouts_layout_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_layout_translation` (
  `layout_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  PRIMARY KEY (`layout_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_layout_translation_layout` FOREIGN KEY (`layout_id`, `status`) REFERENCES `nglayouts_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_migration_versions`
--

DROP TABLE IF EXISTS `nglayouts_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_migration_versions` (
  `version` varchar(14) NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nglayouts_migration_versions`
--

LOCK TABLES `nglayouts_migration_versions` WRITE;
/*!40000 ALTER TABLE `nglayouts_migration_versions` DISABLE KEYS */;
INSERT INTO `nglayouts_migration_versions` VALUES ('000700','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('000800','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('000900','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('001000','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('001100','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('001200','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('001300','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('010000','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('010200','2020-06-15 12:00:00');
INSERT INTO `nglayouts_migration_versions` VALUES ('010300','2020-06-15 12:00:00');
/*!40000 ALTER TABLE `nglayouts_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nglayouts_role`
--

DROP TABLE IF EXISTS `nglayouts_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(191) NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_role_uuid` (`uuid`,`status`),
  KEY `idx_ngl_role_identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_role_policy`
--

DROP TABLE IF EXISTS `nglayouts_role_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_role_policy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `role_id` int NOT NULL,
  `component` varchar(191) DEFAULT NULL,
  `permission` varchar(191) DEFAULT NULL,
  `limitations` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_role_policy_uuid` (`uuid`,`status`),
  KEY `idx_ngl_role` (`role_id`,`status`),
  KEY `idx_ngl_policy_component` (`component`),
  KEY `idx_ngl_policy_component_permission` (`component`,`permission`),
  CONSTRAINT `fk_ngl_policy_role` FOREIGN KEY (`role_id`, `status`) REFERENCES `nglayouts_role` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule`
--

DROP TABLE IF EXISTS `nglayouts_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `rule_group_id` int NOT NULL,
  `layout_uuid` char(36) DEFAULT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_uuid` (`uuid`,`status`),
  KEY `idx_ngl_related_layout` (`layout_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_condition`
--

DROP TABLE IF EXISTS `nglayouts_rule_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_condition` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_condition_uuid` (`uuid`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_condition_rule`
--

DROP TABLE IF EXISTS `nglayouts_rule_condition_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_condition_rule` (
  `condition_id` int NOT NULL,
  `condition_status` int NOT NULL,
  `rule_id` int NOT NULL,
  `rule_status` int NOT NULL,
  PRIMARY KEY (`condition_id`,`condition_status`),
  KEY `idx_ngl_rule` (`rule_id`,`rule_status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_rule` FOREIGN KEY (`rule_id`, `rule_status`) REFERENCES `nglayouts_rule` (`id`, `status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_rule_condition` FOREIGN KEY (`condition_id`, `condition_status`) REFERENCES `nglayouts_rule_condition` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_condition_rule_group`
--

DROP TABLE IF EXISTS `nglayouts_rule_condition_rule_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_condition_rule_group` (
  `condition_id` int NOT NULL,
  `condition_status` int NOT NULL,
  `rule_group_id` int NOT NULL,
  `rule_group_status` int NOT NULL,
  PRIMARY KEY (`condition_id`,`condition_status`),
  KEY `idx_ngl_rule_group` (`rule_group_id`,`rule_group_status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_group_rule_condition` FOREIGN KEY (`condition_id`, `condition_status`) REFERENCES `nglayouts_rule_condition` (`id`, `status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_group_rule_group` FOREIGN KEY (`rule_group_id`, `rule_group_status`) REFERENCES `nglayouts_rule_group` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_data`
--

DROP TABLE IF EXISTS `nglayouts_rule_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_data` (
  `rule_id` int NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `priority` int NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_group`
--

DROP TABLE IF EXISTS `nglayouts_rule_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `depth` int NOT NULL,
  `path` varchar(191) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_group_uuid` (`uuid`,`status`),
  KEY `idx_ngl_parent_rule_group` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nglayouts_rule_group`
--

LOCK TABLES `nglayouts_rule_group` WRITE;
/*!40000 ALTER TABLE `nglayouts_rule_group` DISABLE KEYS */;
INSERT INTO `nglayouts_rule_group` VALUES (1,1,'00000000-0000-0000-0000-000000000000',0,'/1/',NULL,'Root','');
/*!40000 ALTER TABLE `nglayouts_rule_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nglayouts_rule_group_data`
--

DROP TABLE IF EXISTS `nglayouts_rule_group_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_group_data` (
  `rule_group_id` int NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `priority` int NOT NULL,
  PRIMARY KEY (`rule_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nglayouts_rule_group_data`
--

LOCK TABLES `nglayouts_rule_group_data` WRITE;
/*!40000 ALTER TABLE `nglayouts_rule_group_data` DISABLE KEYS */;
INSERT INTO `nglayouts_rule_group_data` VALUES (1,1,0);
/*!40000 ALTER TABLE `nglayouts_rule_group_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nglayouts_rule_target`
--

DROP TABLE IF EXISTS `nglayouts_rule_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_target` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `rule_id` int NOT NULL,
  `type` varchar(191) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_target_uuid` (`uuid`,`status`),
  KEY `idx_ngl_rule` (`rule_id`,`status`),
  KEY `idx_ngl_target_type` (`type`),
  CONSTRAINT `fk_ngl_target_rule` FOREIGN KEY (`rule_id`, `status`) REFERENCES `nglayouts_rule` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_zone`
--

DROP TABLE IF EXISTS `nglayouts_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_zone` (
  `identifier` varchar(191) NOT NULL,
  `layout_id` int NOT NULL,
  `status` int NOT NULL,
  `root_block_id` int NOT NULL,
  `linked_layout_uuid` char(36) DEFAULT NULL,
  `linked_zone_identifier` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`identifier`,`layout_id`,`status`),
  KEY `idx_ngl_layout` (`layout_id`,`status`),
  KEY `idx_ngl_root_block` (`root_block_id`,`status`),
  KEY `idx_ngl_linked_zone` (`linked_layout_uuid`,`linked_zone_identifier`),
  CONSTRAINT `fk_ngl_zone_block` FOREIGN KEY (`root_block_id`, `status`) REFERENCES `nglayouts_block` (`id`, `status`),
  CONSTRAINT `fk_ngl_zone_layout` FOREIGN KEY (`layout_id`, `status`) REFERENCES `nglayouts_layout` (`id`, `status`)
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

-- Dump completed on 2021-05-15 12:00:00
