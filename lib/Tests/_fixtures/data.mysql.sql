-- MySQL dump 10.13  Distrib 5.7.12, for Linux (x86_64)
--
-- Host: localhost    Database: ngbm
-- ------------------------------------------------------
-- Server version	5.7.12-0ubuntu1

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
INSERT INTO `ngbm_block` VALUES (1,0,1,'top_right',0,'paragraph','default','My block','{\"some_param\": \"some_value\"}');
INSERT INTO `ngbm_block` VALUES (1,1,1,'top_right',0,'paragraph','default','My block','{\"some_param\": \"some_value\"}');
INSERT INTO `ngbm_block` VALUES (2,0,1,'top_right',1,'title','small','My other block','{\"other_param\": \"other_value\"}');
INSERT INTO `ngbm_block` VALUES (2,1,1,'top_right',1,'title','small','My other block','{\"other_param\": \"other_value\"}');
INSERT INTO `ngbm_block` VALUES (3,0,2,'bottom_right',0,'paragraph','large','My third block','{\"test_param\": \"test_value\"}');
INSERT INTO `ngbm_block` VALUES (3,1,2,'bottom_right',0,'paragraph','large','My third block','{\"test_param\": \"test_value\"}');
INSERT INTO `ngbm_block` VALUES (4,0,2,'bottom_right',1,'title','small','My fourth block','{\"the_answer\": 42}');
INSERT INTO `ngbm_block` VALUES (4,1,2,'bottom_right',1,'title','small','My fourth block','{\"the_answer\": 42}');
INSERT INTO `ngbm_block` VALUES (5,0,1,'top_right',2,'title','small','My fourth block','{\"the_answer\": 42}');
INSERT INTO `ngbm_block` VALUES (5,1,1,'top_right',2,'title','small','My fourth block','{\"the_answer\": 42}');
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
/*!40000 ALTER TABLE `ngbm_block_collection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_collection`
--

LOCK TABLES `ngbm_collection` WRITE;
/*!40000 ALTER TABLE `ngbm_collection` DISABLE KEYS */;
INSERT INTO `ngbm_collection` VALUES (1,0,0,NULL);
INSERT INTO `ngbm_collection` VALUES (2,1,1,NULL);
INSERT INTO `ngbm_collection` VALUES (3,0,2,'My collection');
INSERT INTO `ngbm_collection` VALUES (3,1,2,'My collection');
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
INSERT INTO `ngbm_collection_item` VALUES (6,1,2,5,1,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (7,0,3,2,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (7,1,3,2,0,'70','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (8,0,3,3,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (8,1,3,3,0,'71','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (9,0,3,5,0,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (9,1,3,5,0,'72','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (10,0,3,7,1,'154','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (10,1,3,7,1,'154','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (11,0,3,8,1,'155','ezcontent');
INSERT INTO `ngbm_collection_item` VALUES (11,1,3,8,1,'155','ezcontent');
/*!40000 ALTER TABLE `ngbm_collection_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_collection_query`
--

LOCK TABLES `ngbm_collection_query` WRITE;
/*!40000 ALTER TABLE `ngbm_collection_query` DISABLE KEYS */;
INSERT INTO `ngbm_collection_query` VALUES (1,1,2,0,'default','ezcontent_search','{\"param\": \"value\"}');
INSERT INTO `ngbm_collection_query` VALUES (2,0,3,0,'default','ezcontent_search','{\"param\": \"value\"}');
INSERT INTO `ngbm_collection_query` VALUES (2,1,3,0,'default','ezcontent_search','{\"param\": \"value\"}');
INSERT INTO `ngbm_collection_query` VALUES (3,0,3,1,'featured','ezcontent_search','{\"param\": \"value\"}');
INSERT INTO `ngbm_collection_query` VALUES (3,1,3,1,'featured','ezcontent_search','{\"param\": \"value\"}');
/*!40000 ALTER TABLE `ngbm_collection_query` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_layout`
--

LOCK TABLES `ngbm_layout` WRITE;
/*!40000 ALTER TABLE `ngbm_layout` DISABLE KEYS */;
INSERT INTO `ngbm_layout` VALUES (1,0,NULL,'3_zones_a','My layout',1447065813,1447065813);
INSERT INTO `ngbm_layout` VALUES (1,1,NULL,'3_zones_a','My layout',1447065813,1447065813);
INSERT INTO `ngbm_layout` VALUES (2,0,NULL,'3_zones_b','My other layout',1447065813,1447065813);
INSERT INTO `ngbm_layout` VALUES (2,1,NULL,'3_zones_b','My other layout',1447065813,1447065813);
/*!40000 ALTER TABLE `ngbm_layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_rule`
--

LOCK TABLES `ngbm_rule` WRITE;
/*!40000 ALTER TABLE `ngbm_rule` DISABLE KEYS */;
INSERT INTO `ngbm_rule` VALUES (1,1,'route');
INSERT INTO `ngbm_rule` VALUES (2,2,'route');
INSERT INTO `ngbm_rule` VALUES (3,3,'route');
INSERT INTO `ngbm_rule` VALUES (4,1,'route_prefix');
INSERT INTO `ngbm_rule` VALUES (5,2,'route_prefix');
INSERT INTO `ngbm_rule` VALUES (6,3,'route_prefix');
INSERT INTO `ngbm_rule` VALUES (7,4,'path_info');
INSERT INTO `ngbm_rule` VALUES (8,5,'path_info_prefix');
INSERT INTO `ngbm_rule` VALUES (9,6,'request_uri');
INSERT INTO `ngbm_rule` VALUES (10,7,'request_uri_prefix');
INSERT INTO `ngbm_rule` VALUES (11,11,'location');
INSERT INTO `ngbm_rule` VALUES (12,12,'location');
INSERT INTO `ngbm_rule` VALUES (13,13,'location');
INSERT INTO `ngbm_rule` VALUES (14,14,'content');
INSERT INTO `ngbm_rule` VALUES (15,15,'content');
INSERT INTO `ngbm_rule` VALUES (16,16,'children');
INSERT INTO `ngbm_rule` VALUES (17,17,'children');
INSERT INTO `ngbm_rule` VALUES (18,18,'subtree');
INSERT INTO `ngbm_rule` VALUES (19,19,'subtree');
INSERT INTO `ngbm_rule` VALUES (20,20,'semantic_path_info');
INSERT INTO `ngbm_rule` VALUES (21,21,'semantic_path_info_prefix');
/*!40000 ALTER TABLE `ngbm_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_rule_condition`
--

LOCK TABLES `ngbm_rule_condition` WRITE;
/*!40000 ALTER TABLE `ngbm_rule_condition` DISABLE KEYS */;
INSERT INTO `ngbm_rule_condition` VALUES (1,2,'route_parameter','{\"some_param\": [1,2]}');
INSERT INTO `ngbm_rule_condition` VALUES (2,3,'route_parameter','{\"some_param\": [3,4]}');
INSERT INTO `ngbm_rule_condition` VALUES (3,3,'route_parameter','{\"some_other_param\": [5,6]}');
/*!40000 ALTER TABLE `ngbm_rule_condition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_rule_value`
--

LOCK TABLES `ngbm_rule_value` WRITE;
/*!40000 ALTER TABLE `ngbm_rule_value` DISABLE KEYS */;
INSERT INTO `ngbm_rule_value` VALUES (1,1,'my_cool_route');
INSERT INTO `ngbm_rule_value` VALUES (2,1,'my_other_cool_route');
INSERT INTO `ngbm_rule_value` VALUES (3,2,'my_second_cool_route');
INSERT INTO `ngbm_rule_value` VALUES (4,2,'my_third_cool_route');
INSERT INTO `ngbm_rule_value` VALUES (5,3,'my_fourth_cool_route');
INSERT INTO `ngbm_rule_value` VALUES (6,3,'my_fifth_cool_route');
INSERT INTO `ngbm_rule_value` VALUES (7,4,'my_cool_');
INSERT INTO `ngbm_rule_value` VALUES (8,4,'my_other_cool_');
INSERT INTO `ngbm_rule_value` VALUES (9,5,'my_second_cool_');
INSERT INTO `ngbm_rule_value` VALUES (10,5,'my_third_cool_');
INSERT INTO `ngbm_rule_value` VALUES (11,6,'my_fourth_cool_');
INSERT INTO `ngbm_rule_value` VALUES (12,6,'my_fifth_cool_');
INSERT INTO `ngbm_rule_value` VALUES (13,7,'/the/answer');
INSERT INTO `ngbm_rule_value` VALUES (14,7,'/the/other/answer');
INSERT INTO `ngbm_rule_value` VALUES (15,8,'/the/');
INSERT INTO `ngbm_rule_value` VALUES (16,8,'/a/');
INSERT INTO `ngbm_rule_value` VALUES (17,9,'/the/answer?a=42');
INSERT INTO `ngbm_rule_value` VALUES (18,9,'/the/answer?a=43');
INSERT INTO `ngbm_rule_value` VALUES (19,10,'/the/');
INSERT INTO `ngbm_rule_value` VALUES (20,10,'/a/');
INSERT INTO `ngbm_rule_value` VALUES (21,11,'42');
INSERT INTO `ngbm_rule_value` VALUES (22,11,'43');
INSERT INTO `ngbm_rule_value` VALUES (23,12,'44');
INSERT INTO `ngbm_rule_value` VALUES (24,12,'45');
INSERT INTO `ngbm_rule_value` VALUES (25,13,'46');
INSERT INTO `ngbm_rule_value` VALUES (26,13,'47');
INSERT INTO `ngbm_rule_value` VALUES (27,14,'48');
INSERT INTO `ngbm_rule_value` VALUES (28,14,'49');
INSERT INTO `ngbm_rule_value` VALUES (29,15,'50');
INSERT INTO `ngbm_rule_value` VALUES (30,15,'51');
INSERT INTO `ngbm_rule_value` VALUES (31,16,'52');
INSERT INTO `ngbm_rule_value` VALUES (32,16,'53');
INSERT INTO `ngbm_rule_value` VALUES (33,17,'54');
INSERT INTO `ngbm_rule_value` VALUES (34,17,'55');
INSERT INTO `ngbm_rule_value` VALUES (35,18,'2');
INSERT INTO `ngbm_rule_value` VALUES (36,18,'3');
INSERT INTO `ngbm_rule_value` VALUES (37,19,'4');
INSERT INTO `ngbm_rule_value` VALUES (38,19,'5');
INSERT INTO `ngbm_rule_value` VALUES (39,20,'/the/answer');
INSERT INTO `ngbm_rule_value` VALUES (40,20,'/the/other/answer');
INSERT INTO `ngbm_rule_value` VALUES (41,21,'/the/');
INSERT INTO `ngbm_rule_value` VALUES (42,21,'/a/');
/*!40000 ALTER TABLE `ngbm_rule_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ngbm_zone`
--

LOCK TABLES `ngbm_zone` WRITE;
/*!40000 ALTER TABLE `ngbm_zone` DISABLE KEYS */;
INSERT INTO `ngbm_zone` VALUES ('bottom',1,0);
INSERT INTO `ngbm_zone` VALUES ('top_left',1,0);
INSERT INTO `ngbm_zone` VALUES ('top_right',1,0);
INSERT INTO `ngbm_zone` VALUES ('bottom',1,1);
INSERT INTO `ngbm_zone` VALUES ('top_left',1,1);
INSERT INTO `ngbm_zone` VALUES ('top_right',1,1);
INSERT INTO `ngbm_zone` VALUES ('bottom_left',2,0);
INSERT INTO `ngbm_zone` VALUES ('bottom_right',2,0);
INSERT INTO `ngbm_zone` VALUES ('top',2,0);
INSERT INTO `ngbm_zone` VALUES ('bottom_left',2,1);
INSERT INTO `ngbm_zone` VALUES ('bottom_right',2,1);
INSERT INTO `ngbm_zone` VALUES ('top',2,1);
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

-- Dump completed on 2016-05-14 20:30:59
