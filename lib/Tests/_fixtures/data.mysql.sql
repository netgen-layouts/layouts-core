-- MySQL dump 10.13  Distrib 5.7.12, for Linux (x86_64)
--
-- Host: localhost    Database: ngbm
-- ------------------------------------------------------
-- Server version	5.7.12-0ubuntu1.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `ngbm_block`
--

LOCK TABLES `ngbm_block` WRITE;
/*!40000 ALTER TABLE `ngbm_block` DISABLE KEYS */;
INSERT INTO `ngbm_block` VALUES (1,0,1,'right',0,'list','list','standard','My block','{\"number_of_columns\": 2}');
INSERT INTO `ngbm_block` VALUES (1,1,1,'right',0,'list','grid','standard_with_intro','My published block','{\"number_of_columns\": 3}');
INSERT INTO `ngbm_block` VALUES (2,0,1,'left',0,'list','grid','standard','My other block','{\"number_of_columns\": 3}');
INSERT INTO `ngbm_block` VALUES (2,1,1,'left',0,'list','grid','standard','My other block','{\"number_of_columns\": 3}');
INSERT INTO `ngbm_block` VALUES (3,0,2,'right',0,'text','text','standard','My third block','{\"content\": \"Text\"}');
INSERT INTO `ngbm_block` VALUES (3,1,2,'right',0,'text','text','standard','My third block','{\"content\": \"Text\"}');
INSERT INTO `ngbm_block` VALUES (4,0,2,'left',0,'title','title','standard','My fourth block','{\"tag\": \"h3\", \"title\": \"Title\"}');
INSERT INTO `ngbm_block` VALUES (4,1,2,'left',0,'title','title','standard','My fourth block','{\"tag\": \"h3\", \"title\": \"Title\"}');
INSERT INTO `ngbm_block` VALUES (5,0,1,'right',1,'list','grid','standard','My fourth block','{\"number_of_columns\": 3}');
INSERT INTO `ngbm_block` VALUES (5,1,1,'right',1,'list','grid','standard','My fourth block','{\"number_of_columns\": 3}');
INSERT INTO `ngbm_block` VALUES (6,0,2,'right',1,'title','title','standard','My sixth block','{\"tag\": \"h3\", \"title\": \"Title\", \"css_class\": \"CSS class\"}');
/*!40000 ALTER TABLE `ngbm_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_block_collection`
--

LOCK TABLES `ngbm_block_collection` WRITE;
/*!40000 ALTER TABLE `ngbm_block_collection` DISABLE KEYS */;
INSERT INTO `ngbm_block_collection` VALUES (1,0,1,0,'default',0,NULL);
INSERT INTO `ngbm_block_collection` VALUES (1,0,3,1,'featured',0,NULL);
INSERT INTO `ngbm_block_collection` VALUES (1,1,2,1,'default',0,NULL);
INSERT INTO `ngbm_block_collection` VALUES (1,1,3,1,'featured',0,NULL);
INSERT INTO `ngbm_block_collection` VALUES (2,0,3,1,'default',0,NULL);
INSERT INTO `ngbm_block_collection` VALUES (2,1,3,1,'default',0,NULL);
INSERT INTO `ngbm_block_collection` VALUES (5,0,4,0,'default',0,NULL);
INSERT INTO `ngbm_block_collection` VALUES (5,1,4,1,'default',0,NULL);
/*!40000 ALTER TABLE `ngbm_block_collection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_collection`
--

LOCK TABLES `ngbm_collection` WRITE;
/*!40000 ALTER TABLE `ngbm_collection` DISABLE KEYS */;
INSERT INTO `ngbm_collection` VALUES (1,0,0,0,NULL);
INSERT INTO `ngbm_collection` VALUES (2,1,1,0,NULL);
INSERT INTO `ngbm_collection` VALUES (3,0,1,1,'My collection');
INSERT INTO `ngbm_collection` VALUES (3,1,1,1,'My collection');
INSERT INTO `ngbm_collection` VALUES (4,0,1,0,NULL);
INSERT INTO `ngbm_collection` VALUES (4,1,1,0,NULL);
INSERT INTO `ngbm_collection` VALUES (5,0,1,1,'My other collection');
/*!40000 ALTER TABLE `ngbm_collection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_collection_item`
--

LOCK TABLES `ngbm_collection_item` WRITE;
/*!40000 ALTER TABLE `ngbm_collection_item` DISABLE KEYS */;
INSERT INTO `ngbm_collection_item` VALUES (1,0,1,0,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (2,0,1,1,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (3,0,1,2,0,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (4,1,2,1,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (5,1,2,2,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (6,1,2,5,0,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (7,0,3,2,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (7,1,3,2,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (8,0,3,3,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (8,1,3,3,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (9,0,3,5,0,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (9,1,3,5,0,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (10,0,4,2,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (10,1,4,2,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (11,0,4,3,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (11,1,4,3,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (12,0,4,5,0,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (12,1,4,5,0,'72','ezcontent');
/*!40000 ALTER TABLE `ngbm_collection_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_collection_query`
--

LOCK TABLES `ngbm_collection_query` WRITE;
/*!40000 ALTER TABLE `ngbm_collection_query` DISABLE KEYS */;
INSERT INTO `ngbm_collection_query` VALUES (1,1,2,0,'default','ezcontent_search','{\"parent_location_id\": 2, \"sort_direction\": \"descending\", \"sort_type\": \"date_published\", \"offset\": 0, \"query_type\": \"list\"}');
INSERT INTO `ngbm_collection_query` VALUES (2,0,3,0,'default','ezcontent_search','{\"parent_location_id\": 2, \"sort_direction\": \"descending\", \"sort_type\": \"date_published\", \"offset\": 0, \"query_type\": \"list\"}');
INSERT INTO `ngbm_collection_query` VALUES (2,1,3,0,'default','ezcontent_search','{\"parent_location_id\": 2, \"sort_direction\": \"descending\", \"sort_type\": \"date_published\", \"offset\": 0, \"query_type\": \"list\"}');
INSERT INTO `ngbm_collection_query` VALUES (3,0,3,1,'featured','ezcontent_search','{\"parent_location_id\": 2, \"sort_direction\": \"descending\", \"sort_type\": \"date_published\", \"offset\": 0, \"query_type\": \"list\"}');
INSERT INTO `ngbm_collection_query` VALUES (3,1,3,1,'featured','ezcontent_search','{\"parent_location_id\": 2, \"sort_direction\": \"descending\", \"sort_type\": \"date_published\", \"offset\": 0, \"query_type\": \"list\"}');
INSERT INTO `ngbm_collection_query` VALUES (4,0,4,0,'default','ezcontent_search','{\"parent_location_id\": 71, \"sort_direction\": \"descending\", \"sort_type\": \"date_published\", \"offset\": 0, \"limit\": 3, \"query_type\": \"list\"}');
INSERT INTO `ngbm_collection_query` VALUES (4,1,4,0,'default','ezcontent_search','{\"parent_location_id\": 71, \"sort_direction\": \"descending\", \"sort_type\": \"date_published\", \"offset\": 0, \"limit\": 3, \"query_type\": \"list\"}');
/*!40000 ALTER TABLE `ngbm_collection_query` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_layout`
--

LOCK TABLES `ngbm_layout` WRITE;
/*!40000 ALTER TABLE `ngbm_layout` DISABLE KEYS */;
INSERT INTO `ngbm_layout` VALUES (1,0,'4_zones_a','My layout',1447065813,1447065813,0);
INSERT INTO `ngbm_layout` VALUES (1,1,'4_zones_a','My layout',1447065813,1447065813,0);
INSERT INTO `ngbm_layout` VALUES (2,0,'4_zones_b','My other layout',1447065813,1447065813,0);
INSERT INTO `ngbm_layout` VALUES (2,1,'4_zones_b','My other layout',1447065813,1447065813,0);
INSERT INTO `ngbm_layout` VALUES (3,0,'4_zones_b','My third layout',1447065813,1447065813,1);
INSERT INTO `ngbm_layout` VALUES (3,1,'4_zones_b','My third layout',1447065813,1447065813,1);
INSERT INTO `ngbm_layout` VALUES (4,0,'4_zones_b','My fourth layout',1447065813,1447065813,0);
INSERT INTO `ngbm_layout` VALUES (5,1,'4_zones_b','My fifth layout',1447065813,1447065813,1);
INSERT INTO `ngbm_layout` VALUES (6,1,'4_zones_b','My sixth layout',1447065813,1447065813,0);
/*!40000 ALTER TABLE `ngbm_layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_rule`
--

LOCK TABLES `ngbm_rule` WRITE;
/*!40000 ALTER TABLE `ngbm_rule` DISABLE KEYS */;
INSERT INTO `ngbm_rule` VALUES (1,1,1,'My comment');
INSERT INTO `ngbm_rule` VALUES (2,1,2,'My other comment');
INSERT INTO `ngbm_rule` VALUES (3,1,3,NULL);
INSERT INTO `ngbm_rule` VALUES (4,1,1,NULL);
INSERT INTO `ngbm_rule` VALUES (5,0,2,NULL);
INSERT INTO `ngbm_rule` VALUES (5,1,2,NULL);
INSERT INTO `ngbm_rule` VALUES (6,1,3,NULL);
INSERT INTO `ngbm_rule` VALUES (7,0,4,NULL);
INSERT INTO `ngbm_rule` VALUES (7,1,4,NULL);
INSERT INTO `ngbm_rule` VALUES (8,1,5,NULL);
INSERT INTO `ngbm_rule` VALUES (9,1,6,NULL);
INSERT INTO `ngbm_rule` VALUES (10,1,7,NULL);
INSERT INTO `ngbm_rule` VALUES (11,1,11,NULL);
INSERT INTO `ngbm_rule` VALUES (12,0,12,NULL);
INSERT INTO `ngbm_rule` VALUES (13,1,13,NULL);
INSERT INTO `ngbm_rule` VALUES (14,1,14,NULL);
INSERT INTO `ngbm_rule` VALUES (15,1,NULL,NULL);
INSERT INTO `ngbm_rule` VALUES (16,1,16,NULL);
INSERT INTO `ngbm_rule` VALUES (17,1,17,NULL);
INSERT INTO `ngbm_rule` VALUES (18,1,1,NULL);
INSERT INTO `ngbm_rule` VALUES (19,1,19,NULL);
INSERT INTO `ngbm_rule` VALUES (20,1,20,NULL);
INSERT INTO `ngbm_rule` VALUES (21,1,21,NULL);
/*!40000 ALTER TABLE `ngbm_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_rule_condition`
--

LOCK TABLES `ngbm_rule_condition` WRITE;
/*!40000 ALTER TABLE `ngbm_rule_condition` DISABLE KEYS */;
INSERT INTO `ngbm_rule_condition` VALUES (1,1,2,'route_parameter','{\"parameter_name\": \"some_param\", \"parameter_values\": [1,2]}');
INSERT INTO `ngbm_rule_condition` VALUES (2,1,3,'route_parameter','{\"parameter_name\": \"some_param\", \"parameter_values\": [3,4]}');
INSERT INTO `ngbm_rule_condition` VALUES (3,1,3,'route_parameter','{\"parameter_name\": \"some_other_param\", \"parameter_values\": [5,6]}');
INSERT INTO `ngbm_rule_condition` VALUES (4,0,5,'ez_site_access','[\"cro\"]');
INSERT INTO `ngbm_rule_condition` VALUES (4,1,5,'ez_site_access','[\"cro\"]');
/*!40000 ALTER TABLE `ngbm_rule_condition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_rule_data`
--

LOCK TABLES `ngbm_rule_data` WRITE;
/*!40000 ALTER TABLE `ngbm_rule_data` DISABLE KEYS */;
INSERT INTO `ngbm_rule_data` VALUES (1,1,20);
INSERT INTO `ngbm_rule_data` VALUES (2,1,19);
INSERT INTO `ngbm_rule_data` VALUES (3,1,18);
INSERT INTO `ngbm_rule_data` VALUES (4,0,17);
INSERT INTO `ngbm_rule_data` VALUES (5,1,16);
INSERT INTO `ngbm_rule_data` VALUES (6,1,15);
INSERT INTO `ngbm_rule_data` VALUES (7,1,14);
INSERT INTO `ngbm_rule_data` VALUES (8,1,13);
INSERT INTO `ngbm_rule_data` VALUES (9,1,12);
INSERT INTO `ngbm_rule_data` VALUES (10,1,11);
INSERT INTO `ngbm_rule_data` VALUES (11,1,10);
INSERT INTO `ngbm_rule_data` VALUES (12,1,9);
INSERT INTO `ngbm_rule_data` VALUES (13,1,8);
INSERT INTO `ngbm_rule_data` VALUES (14,1,7);
INSERT INTO `ngbm_rule_data` VALUES (15,0,6);
INSERT INTO `ngbm_rule_data` VALUES (16,0,5);
INSERT INTO `ngbm_rule_data` VALUES (17,1,4);
INSERT INTO `ngbm_rule_data` VALUES (18,1,3);
INSERT INTO `ngbm_rule_data` VALUES (19,1,2);
INSERT INTO `ngbm_rule_data` VALUES (20,1,1);
INSERT INTO `ngbm_rule_data` VALUES (21,1,0);
/*!40000 ALTER TABLE `ngbm_rule_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_rule_target`
--

LOCK TABLES `ngbm_rule_target` WRITE;
/*!40000 ALTER TABLE `ngbm_rule_target` DISABLE KEYS */;
INSERT INTO `ngbm_rule_target` VALUES (1,1,1,'route','my_cool_route');
INSERT INTO `ngbm_rule_target` VALUES (2,1,1,'route','my_other_cool_route');
INSERT INTO `ngbm_rule_target` VALUES (3,1,2,'route','my_second_cool_route');
INSERT INTO `ngbm_rule_target` VALUES (4,1,2,'route','my_third_cool_route');
INSERT INTO `ngbm_rule_target` VALUES (5,1,3,'route','my_fourth_cool_route');
INSERT INTO `ngbm_rule_target` VALUES (6,1,3,'route','my_fifth_cool_route');
INSERT INTO `ngbm_rule_target` VALUES (7,1,4,'route_prefix','my_cool_');
INSERT INTO `ngbm_rule_target` VALUES (8,1,4,'route_prefix','my_other_cool_');
INSERT INTO `ngbm_rule_target` VALUES (9,0,5,'route_prefix','my_second_cool_');
INSERT INTO `ngbm_rule_target` VALUES (9,1,5,'route_prefix','my_second_cool_');
INSERT INTO `ngbm_rule_target` VALUES (10,0,5,'route_prefix','my_third_cool_');
INSERT INTO `ngbm_rule_target` VALUES (10,1,5,'route_prefix','my_third_cool_');
INSERT INTO `ngbm_rule_target` VALUES (11,1,6,'route_prefix','my_fourth_cool_');
INSERT INTO `ngbm_rule_target` VALUES (12,1,6,'route_prefix','my_fifth_cool_');
INSERT INTO `ngbm_rule_target` VALUES (13,0,7,'path_info','/the/answer');
INSERT INTO `ngbm_rule_target` VALUES (13,1,7,'path_info','/the/answer');
INSERT INTO `ngbm_rule_target` VALUES (14,0,7,'path_info','/the/other/answer');
INSERT INTO `ngbm_rule_target` VALUES (14,1,7,'path_info','/the/other/answer');
INSERT INTO `ngbm_rule_target` VALUES (15,1,8,'path_info_prefix','/the/');
INSERT INTO `ngbm_rule_target` VALUES (16,1,8,'path_info_prefix','/a/');
INSERT INTO `ngbm_rule_target` VALUES (17,1,9,'request_uri','/the/answer?a=42');
INSERT INTO `ngbm_rule_target` VALUES (18,1,9,'request_uri','/the/answer?a=43');
INSERT INTO `ngbm_rule_target` VALUES (19,1,10,'request_uri_prefix','/the/');
INSERT INTO `ngbm_rule_target` VALUES (20,1,10,'request_uri_prefix','/a/');
INSERT INTO `ngbm_rule_target` VALUES (21,1,11,'ezlocation','72');
INSERT INTO `ngbm_rule_target` VALUES (22,1,11,'ezlocation','73');
INSERT INTO `ngbm_rule_target` VALUES (23,0,12,'ezlocation','74');
INSERT INTO `ngbm_rule_target` VALUES (24,1,13,'ezlocation','75');
INSERT INTO `ngbm_rule_target` VALUES (25,1,13,'ezlocation','155');
INSERT INTO `ngbm_rule_target` VALUES (26,1,13,'ezlocation','156');
INSERT INTO `ngbm_rule_target` VALUES (27,1,14,'ezcontent','70');
INSERT INTO `ngbm_rule_target` VALUES (28,1,14,'ezcontent','71');
INSERT INTO `ngbm_rule_target` VALUES (29,1,15,'ezcontent','72');
INSERT INTO `ngbm_rule_target` VALUES (30,1,15,'ezcontent','73');
INSERT INTO `ngbm_rule_target` VALUES (33,1,17,'ezchildren','72');
INSERT INTO `ngbm_rule_target` VALUES (34,1,17,'ezchildren','73');
INSERT INTO `ngbm_rule_target` VALUES (35,1,18,'ezsubtree','2');
INSERT INTO `ngbm_rule_target` VALUES (36,1,18,'ezsubtree','43');
INSERT INTO `ngbm_rule_target` VALUES (37,1,19,'ezsubtree','5');
INSERT INTO `ngbm_rule_target` VALUES (38,1,19,'ezsubtree','13');
INSERT INTO `ngbm_rule_target` VALUES (39,1,20,'ez_semantic_path_info','/the/answer');
INSERT INTO `ngbm_rule_target` VALUES (40,1,20,'ez_semantic_path_info','/the/other/answer');
INSERT INTO `ngbm_rule_target` VALUES (41,1,21,'ez_semantic_path_info_prefix','/the/');
INSERT INTO `ngbm_rule_target` VALUES (42,1,21,'ez_semantic_path_info_prefix','/a/');
/*!40000 ALTER TABLE `ngbm_rule_target` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_zone`
--

LOCK TABLES `ngbm_zone` WRITE;
/*!40000 ALTER TABLE `ngbm_zone` DISABLE KEYS */;
INSERT INTO `ngbm_zone` VALUES ('bottom',1,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',1,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',2,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',2,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',3,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',3,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',4,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',5,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('bottom',6,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',1,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',1,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',2,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',2,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',3,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',3,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',4,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',5,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('left',6,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',1,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',1,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',2,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',2,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',3,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',3,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',4,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',5,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('right',6,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('top',1,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('top',1,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('top',2,0,3,'top');
INSERT INTO `ngbm_zone` VALUES ('top',2,1,3,'top');
INSERT INTO `ngbm_zone` VALUES ('top',3,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('top',3,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('top',4,0,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('top',5,1,NULL,NULL);
INSERT INTO `ngbm_zone` VALUES ('top',6,1,NULL,NULL);
/*!40000 ALTER TABLE `ngbm_zone` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-07 15:54:19
