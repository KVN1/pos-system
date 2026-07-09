-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: infinite_pos
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `log_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,1,'Returned Sale','Sale ID: 31','2025-10-15 21:53:07'),(2,1,'Returned Sale','Sale ID: 20','2025-10-15 21:54:16'),(3,1,'Returned Sale','Sale ID: 26','2025-10-15 21:54:24'),(4,1,'Returned Sale','Sale ID: 30','2025-10-15 21:54:57'),(5,1,'Add Sale','Sale ID: 39, Total: ₱18, Payment: gcash','2025-10-15 22:08:41'),(6,1,'Add Product','Added product: Mangosteen plus guyabano coffee 21g (111111112121)','2025-10-15 22:12:48'),(7,1,'Edit Product','Edited product: Mangosteen plus guyabano coffee 21g (111111112121)','2025-10-15 22:13:59'),(9,1,'Archive Product','Archived product ID: 24','2025-10-15 22:19:49'),(10,1,'Add Sale','Sale ID: 40, Total: ₱26000, Payment: cash','2025-10-18 20:35:30'),(11,1,'Returned Sale','Sale ID: 40','2025-10-18 20:36:01'),(12,1,'Archive Product','Archived product ID: 25','2025-10-18 20:36:51'),(13,1,'Edit Category','Edited category ID 1 to: Fruits ; Vegetables','2025-10-18 20:37:07'),(14,1,'Add Category','Added category: mike test','2025-10-18 20:37:20'),(15,4,'Add Sale','Sale ID: 41, Total: ₱190, Payment: cash','2025-10-18 21:43:27'),(16,4,'Add Sale','Sale ID: 42, Total: ₱18, Payment: cash','2025-10-18 22:15:13'),(17,4,'Reorder Stock','Reordered 1 units of product ID: 16','2025-10-18 22:25:51'),(18,4,'Add Sale','Sale ID: 43, Total: ₱30, Payment: cash','2025-10-18 22:26:45'),(19,4,'Reorder Stock','Reordered 12 units of product ID: 16','2025-10-18 22:27:03'),(20,4,'Add Product','Added product: tomato (123738493)','2025-10-18 22:29:33'),(21,4,'Edit Product','Edited product: tomato (123738493)','2025-10-18 22:29:59'),(22,1,'Deactivate Category','Deactivated category ID 45','2025-10-19 22:44:27'),(23,1,'Restore Category','Restored category ID 45','2025-10-19 22:44:44'),(24,1,'Restore Category','Restored category ID 45','2025-10-19 22:45:31'),(25,1,'Deactivate Category','Deactivated category ID 45','2025-10-19 22:48:11'),(26,1,'Restore Category','Restored category ID 45','2025-10-19 22:48:17'),(27,1,'Add Sale','Sale ID: 44, Total: ₱13000, Payment: cash','2025-10-22 18:58:27'),(28,1,'Add Sale','Sale ID: 45, Total: ₱190, Payment: cash','2025-10-22 19:14:00'),(29,1,'Add Sale','Sale ID: 46, Total: ₱12350, Payment: cash','2025-10-22 19:22:36'),(30,1,'Add Sale','Sale ID: 47, Total: ₱37050, Payment: cash','2025-10-22 19:26:21'),(31,1,'Add Sale','Sale ID: 48, Total: ₱13148, Payment: cash','2025-10-22 19:28:45'),(32,1,'Deactivate Category','Deactivated category ID 45','2025-10-22 19:32:49'),(33,1,'Deactivate Category','Deactivated category ID 45','2025-10-22 19:32:52'),(34,1,'Deactivate Category','Deactivated category ID 45','2025-10-22 19:33:47'),(35,1,'Deactivate Category','Deactivated category ID 45','2025-10-22 19:33:49'),(36,1,'Add Sale','Sale ID: 49, Total: ₱74100, Payment: cash','2025-10-22 19:35:30'),(37,1,'Add Category','Added category: Vet','2025-10-22 19:50:59'),(38,1,'Deactivate Category','Deactivated category ID 45','2025-10-22 19:51:04'),(39,1,'Deactivate Category','Deactivated category ID 46','2025-10-22 19:53:36'),(40,1,'Deactivate Category','Deactivated category ID 39','2025-10-22 19:54:46'),(41,1,'Restore Category','Restored category ID 46','2025-10-22 19:55:00'),(42,1,'Deactivate Category','Deactivated category ID 46','2025-10-22 19:55:38'),(43,1,'Deactivate Category','Deactivated category ID 25','2025-10-22 19:56:47'),(44,1,'Restore Category','Restored category ID 39','2025-10-22 19:56:54'),(45,1,'Deactivate Category','Deactivated category ID 43','2025-10-22 20:07:58'),(46,1,'Deactivate Category','Deactivated category ID 44','2025-10-22 20:08:00'),(47,1,'Deactivate Category','Deactivated category ID 32','2025-10-22 20:08:01'),(48,1,'Deactivate Category','Deactivated category ID 33','2025-10-22 20:11:50'),(49,1,'Add Sale','Sale ID: 50, Total: ₱2200, Payment: gcash','2025-10-22 20:13:56'),(50,1,'Add Sale','Sale ID: 51, Total: ₱37050, Payment: gcash','2025-10-23 21:56:00'),(51,1,'Add Sale','Sale ID: 52, Total: ₱220, Payment: cash','2025-10-23 22:07:11'),(52,1,'Edit Category','Edited category ID 13 to: Beeff','2025-10-23 22:36:18'),(53,1,'Edit Product','Edited product ID 29: tipalia (Code: 5123123)','2025-10-23 22:36:52'),(54,1,'Edit Product','Edited product ID 6: Luxe Organix Aloe Vera 100ml (Code: 4806524039887)','2025-10-23 22:37:09'),(55,1,'Returned Sale','Sale ID: 38','2025-10-23 22:39:13'),(56,1,'Edit Product','Edited product ID 29: tipalia (Code: 5123123)','2025-10-23 22:40:06'),(57,1,'Reorder Stock','Reordered 100 units of product ID 29 (tipalia)','2025-10-23 22:40:14'),(58,1,'Archive Product','Archived product ID 29','2025-10-23 22:40:22'),(59,1,'Restore Product','Restored product ID 29','2025-10-23 22:40:34'),(60,1,'Add Sale','Sale ID: 53, Total: ₱97.5, Payment: cash','2025-10-23 22:49:06'),(61,1,'Add Sale','Sale ID: 54, Total: ₱387.2, Payment: cash','2025-10-23 22:51:30'),(62,1,'Edit Product','Edited product ID 29: tipalia (Code: 5123123)','2025-10-23 22:52:15'),(63,1,'Add Sale','Sale ID: 55, Total: ₱522.5, Payment: cash','2025-10-24 19:58:20'),(64,1,'Add Product','Added product ID 4800131591004: Biogenic 70% 330ml','2025-10-24 20:04:16'),(65,1,'Deactivate Category','Deactivated category ID 38','2025-10-24 20:04:34'),(66,1,'Restore Category','Restored category ID 38','2025-10-24 20:05:07'),(67,1,'Edit Product','Edited product ID 24: testlang1 (Code: 4800758039887)','2025-10-24 20:05:46'),(68,1,'Archive Product','Archived product ID 24','2025-10-24 20:06:06'),(69,1,'Restore Product','Restored product ID 24','2025-10-24 20:07:51'),(70,1,'Archive Product','Archived product ID 24','2025-10-24 20:10:44'),(71,1,'Deactivate Category','Deactivated category ID 39','2025-10-24 20:14:28'),(72,1,'Add Sale','Sale ID: 56, Total: ₱367.4, Payment: cash','2025-10-24 20:18:30'),(73,1,'Add Sale','Sale ID: 57, Total: ₱313.5, Payment: cash','2025-10-24 20:22:42'),(74,1,'Add Sale','Sale ID: 58, Total: ₱110, Payment: cash','2025-10-24 20:23:30'),(75,1,'Edit Product','Edited product ID 25: veggies (Code: 23424637132435)','2025-10-24 20:31:56'),(76,1,'Archive Product','Archived product ID 25','2025-10-24 20:32:13'),(77,1,'Restore Product','Restored product ID 25','2025-10-24 20:32:27'),(78,1,'Restore Product','Restored product ID 24','2025-10-24 20:32:30'),(79,1,'Add Product','Added product ID 426672324123561: another test','2025-10-24 20:33:42'),(80,1,'Archive Product','Archived product ID 24','2025-10-24 20:46:31'),(81,1,'Add Product','Added product ID 412315123: fefgsdf','2025-10-24 20:46:45'),(82,1,'Restore Product','Restored product ID 24','2025-10-24 20:49:29'),(83,1,'Archive Product','Archived product ID 24','2025-10-24 20:49:35'),(84,1,'Add Product','Added product ID qwe134141241: dasdasf','2025-10-24 20:49:50'),(85,1,'Add Product','Added product ID 45611515514: adasda','2025-10-24 21:07:36'),(86,1,'Add Product','Added product ID 14115123: 31fasf','2025-10-24 21:08:10'),(87,1,'Add Product','Added product ID 124516161613451: TESTING','2025-10-24 21:10:11'),(88,1,'Archive Product','Archived product ID 35','2025-10-24 21:10:29'),(89,1,'Edit Category','Edited category ID 13 to: Beef','2025-10-24 21:10:50'),(90,1,'Deactivate Category','Deactivated category ID 35','2025-10-24 21:11:00'),(91,1,'Edit Category','Edited category ID 4 to: Beveragess','2025-10-24 21:18:26'),(92,1,'Edit Category','Edited category ID 13 to: Beeff','2025-10-24 21:19:19'),(93,1,'Deactivate Category','Deactivated category ID 38','2025-10-24 21:19:24'),(94,6,'Add Sale','Sale ID: 59, Total: ₱325, Payment: cash','2025-10-24 21:23:21'),(95,6,'Archive Product','Archived product ID 30','2025-10-24 21:23:49'),(96,6,'Add Product','Added product ID 4800131591004: Biogenic 70% 330ml','2025-10-24 21:24:51'),(97,6,'Edit Product','Edited product ID 37: Biogenic 70% 330ml (Code: 4800131591004)','2025-10-24 21:25:33'),(98,6,'Edit Product','Edited product ID 37: Biogenic 70% 330ml (Code: 4800131591004)','2025-10-24 21:26:00'),(99,6,'Reorder Stock','Reordered 50 units of product ID 37 (Biogenic 70% 330ml)','2025-10-24 21:26:16'),(100,6,'Add Category','Added category: Testing Again','2025-10-24 21:26:40'),(101,6,'Deactivate Category','Deactivated category ID 47','2025-10-24 21:26:48'),(102,6,'Restore Category','Restored category ID 47','2025-10-24 21:27:08'),(103,6,'Edit Category','Edited category ID 13 to: Beef','2025-10-24 21:27:21'),(104,6,'Edit Product','Edited product ID 37: Biogenic 70% 330ml (Code: 4800131591004)','2025-10-24 21:27:42'),(105,6,'Edit Product','Edited product ID 27: tomato (Code: 123738493)','2025-10-24 21:28:22'),(106,6,'Returned Sale','Sale ID: 46','2025-10-24 21:29:42'),(107,6,'Archive Product','Archived product ID 37','2025-10-24 21:35:19'),(108,7,'Add Product','Added product ID 4800131591004: Biogenic 70% 330ml','2025-10-24 21:40:35'),(109,7,'Edit Product','Edited product ID 38: Biogenic 70% 330mll (Code: 4800131591004)','2025-10-24 21:41:22'),(110,7,'Archive Product','Archived product ID 34','2025-10-24 21:41:45'),(111,7,'Add Sale','Sale ID: 60, Total: ₱1599, Payment: cash','2025-10-24 21:44:07'),(112,7,'Edit Product','Edited product ID 36: TESTING (Code: 124516161613451)','2025-10-24 21:44:55'),(113,7,'Reorder Stock','Reordered 50 units of product ID 36 (TESTING)','2025-10-24 21:45:18'),(114,7,'Edit Category','Edited category ID 4 to: Beverages','2025-10-24 21:46:39'),(115,7,'Deactivate Category','Deactivated category ID 42','2025-10-24 21:46:48'),(116,7,'Restore Category','Restored category ID 42','2025-10-24 21:47:32'),(117,7,'Edit Product','Edited product ID 20: Epson 003 ink 65ml (Code: 8885007027876)','2025-10-24 21:48:28'),(118,2,'Add Sale','Sale ID: 61, Total: ₱572, Payment: cash','2025-10-24 22:19:11'),(119,1,'Add Sale','Sale ID: 62, Total: ₱110, Payment: gcash','2025-10-27 21:34:11'),(120,1,'Add Sale','Sale ID: 63, Total: ₱1234, Payment: ','2025-10-27 21:34:45'),(121,1,'Edit Product','Edited product ID 36: TESTING (Code: 124516161613451)','2025-10-28 20:06:42'),(122,1,'Edit Product','Edited product ID 25: veggies (Code: 23424637132435)','2025-10-28 20:12:07'),(123,1,'Edit Product','Stock changed from \'129\' to \'1355\'. Reason: yehhhhhhhhhhhhhhhhhhhhhhhhhh','2025-10-28 20:19:19'),(124,1,'Edit Product','Perishability changed from \'Non-Perishable\' to \'Perishable\'. Reason: non to perishable','2025-10-28 20:33:18'),(125,1,'Add Sale','Sale ID: 64, Total: ₱681.72, Payment: cash','2025-10-28 20:33:59'),(126,1,'Add Sale','Sale ID: 65, Total: ₱3089.476, Payment: cash','2025-10-28 20:41:55'),(127,0,'Update Discount','Updated discount ID 3 to 0%','2025-10-28 20:52:21'),(128,0,'Update Discount','Updated discount ID 3 to 0%','2025-10-28 20:52:23'),(129,0,'Update Discount','Updated discount ID 3 to 0%','2025-10-28 20:52:52'),(130,1,'Add Sale','Sale ID: 66, Total: ₱38245.746, Payment: cash','2025-10-28 21:01:17'),(131,0,'Update Discount','Updated discount ID 3 to 0%','2025-10-28 21:19:31'),(132,0,'Update Discount','Updated discount ID 3 to 0%','2025-10-28 21:19:56'),(133,1,'Update Discount','Updated discount ID 1 to 5%','2025-10-28 21:27:56'),(134,1,'Update Discount','Updated discount ID 1 to 5%','2025-10-28 21:28:48'),(135,1,'Update Discount','Updated discount ID 1 to 5%','2025-10-28 21:30:30'),(136,1,'Update Discount','Updated discount ID 1 to 5%','2025-10-28 21:32:37'),(137,1,'Update Discount','Updated discount ID 1 to 6%','2025-10-28 21:32:42'),(138,1,'Add Sale','Sale ID: 67, Total: ₱12220, Payment: cash','2025-10-28 21:40:18'),(139,1,'Returned Sale','Sale ID: 68','2025-10-28 21:51:44'),(140,1,'Returned Sale','Sale ID: 69','2025-10-28 21:51:49'),(141,1,'Returned Sale','Sale ID: 67','2025-10-28 21:51:56'),(142,1,'Add Sale','Sale ID: 71, Total: ₱1,159.96, Discount: ₱69.60, Payment: cash','2025-10-28 21:56:25'),(143,1,'Returned Sale','Sale ID: 71','2025-10-28 21:59:12'),(144,1,'Add Sale','Sale ID: 72, Total: ₱418.00, Discount: ₱20.90, Payment: cash','2025-10-28 21:59:32'),(145,1,'Add Sale','Sale ID: 73, Total: ₱325.00, Discount: ₱0.00, Payment: cash','2025-10-28 22:03:36'),(146,1,'Add Sale','Sale ID: 74, Total: ₱1172.3, Payment: cash','2025-10-28 22:05:32'),(147,1,'Update Discount','Updated discount ID 2 to 5%','2025-10-28 22:05:46'),(148,1,'Add Sale','Sale ID: 75, Total: ₱206.8, Payment: cash','2025-10-28 22:06:09'),(149,1,'Edit Product','Stock changed from \'42\' to \'50\'. Reason: yesyes','2025-10-28 22:07:50'),(150,1,'Add Sale','Sale ID: 76, Total: ₱103.4, Payment: cash','2025-10-29 21:41:01'),(151,1,'Edit Category','Edited category ID 13 to: Beeff','2025-10-29 22:24:14'),(152,1,'Deactivate User','Deactivated user ID: 4','2025-10-29 22:37:08'),(153,1,'Activate User','Activated user ID: 4','2025-10-29 22:37:15'),(154,1,'Restore Category','Restored category ID 43','2025-10-29 22:37:24'),(155,1,'Edit Product','Category changed from \'Dairy & Eggs\' to \'Beverages\'; Perishability changed from \'N/P\' to \'Non-Perishable\'; Stock changed from \'58\' to \'1\'. Reason: adjust lang','2025-10-29 22:44:34'),(156,1,'Edit Product','Perishability changed from \'\' to \'N/P\'. Reason: adjust lngsss','2025-10-29 22:46:26'),(157,1,'Returned Sale','Sale ID: 63','2025-10-31 18:29:58'),(158,1,'Returned Sale','Sale ID: 76','2025-10-31 18:43:05'),(159,1,'Returned Sale','Sale ID: 75','2025-10-31 18:43:40'),(160,1,'Return Item','Returned item ID: 60 (Product code: 5123123, Qty: 1) from Sale ID: 75','2025-10-31 18:49:48'),(161,1,'Return Item','Returned item ID: 59 (Product code: 124516161613451, Qty: 1) from Sale ID: 74','2025-10-31 19:02:19'),(162,1,'Add Sale','Sale ID: 77, Total: ₱68.25, Payment: cash','2025-10-31 19:05:44'),(163,1,'Add Sale','Sale ID: 78, Total: ₱226.6, Payment: cash','2025-10-31 19:07:06'),(164,1,'Add Sale','Sale ID: 79, Total: ₱221.276, Payment: cash','2025-10-31 19:07:59'),(165,1,'Add Sale','Sale ID: 80, Total: ₱219.21, Payment: cash','2025-10-31 19:15:43'),(166,1,'Add Sale','Sale ID: 81, Total: ₱221.28, Payment: cash','2025-10-31 19:17:18'),(167,1,'Return Sale','Sale ID: 81 | Reason: No reason provided','2025-10-31 19:28:24'),(168,1,'Edit Category','Edited category ID 43 to: a new ones','2025-10-31 19:48:23'),(169,1,'Returned sale ID 61','Reason: yehy','2025-10-31 19:51:46'),(170,1,'Return Item','Returned item ID: 20 (Product code: Z11M-GA49V11-2502000537, Qty: 2) from Sale ID: 40','2025-10-31 19:57:06'),(171,1,'Returned sale ID 47','Reason: deffective','2025-10-31 19:59:30'),(172,1,'Return Item','Returned item ID: 31 (Product code: 6974944461538, Qty: 1) from Sale ID: 48','2025-10-31 19:59:56');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup_settings`
--

DROP TABLE IF EXISTS `backup_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_settings` (
  `id` int(11) NOT NULL,
  `frequency` varchar(20) DEFAULT NULL,
  `backup_time` time DEFAULT NULL,
  `last_backup` datetime DEFAULT NULL,
  `next_backup` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_settings`
--

LOCK TABLES `backup_settings` WRITE;
/*!40000 ALTER TABLE `backup_settings` DISABLE KEYS */;
INSERT INTO `backup_settings` VALUES (1,'hourly','22:12:00','2025-11-04 15:01:19','2025-11-05 00:00:00');
/*!40000 ALTER TABLE `backup_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Fruits ; Vegetables',NULL,'active','2025-03-27 09:43:38','2025-08-28 11:07:58'),(2,'Dairy & Eggs',NULL,'active','2025-03-27 09:43:38','2025-03-27 09:43:38'),(3,'Meat & Seafood',NULL,'active','2025-03-27 09:43:38','2025-03-27 09:43:38'),(4,'Beverages',NULL,'active','2025-03-27 09:43:38','2025-10-24 13:46:39'),(5,'Snacks',NULL,'active','2025-03-27 09:43:38','2025-03-28 13:50:55'),(7,'Leafy Vegetables',1,'active','2025-03-27 09:43:46','2025-08-28 11:07:12'),(8,'Root Crops',1,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(9,'Milk',2,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(10,'Cheese',2,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(11,'Yogurt',2,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(12,'Chicken',3,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(13,'Beeff',3,'active','2025-03-27 09:43:46','2025-10-29 14:24:14'),(14,'Fish',3,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(15,'Shrimp',3,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(16,'Soft Drinks',4,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(17,'Juices',4,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(18,'Coffee',4,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(19,'Tea',4,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(20,'Chips',5,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(21,'Biscuits',5,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(22,'Nuts',5,'active','2025-03-27 09:43:46','2025-03-27 09:43:46'),(23,'Spices',NULL,'active','2025-03-27 09:49:18','2025-03-27 09:49:18'),(24,'Household Supplies',NULL,'active','2025-03-27 12:09:35','2025-03-27 12:09:35'),(25,'testaa',NULL,'inactive','2025-03-27 13:27:57','2025-10-22 11:56:47'),(32,'ano ba ito',NULL,'inactive','2025-03-28 12:23:33','2025-10-22 12:08:00'),(33,'Arrah',NULL,'inactive','2025-03-28 12:27:46','2025-10-22 12:11:49'),(34,'Daniella',NULL,'active','2025-03-28 12:28:57','2025-04-05 09:36:22'),(35,'Sheryl',NULL,'inactive','2025-03-28 12:30:20','2025-10-24 13:11:00'),(36,'Joshua',NULL,'active','2025-03-28 13:51:00','2025-04-05 09:36:39'),(37,'Medical',NULL,'active','2025-03-29 13:19:41','2025-03-29 13:19:41'),(38,'Kevin',NULL,'inactive','2025-03-30 05:35:40','2025-10-24 13:19:24'),(39,'Trina',NULL,'inactive','2025-03-30 09:23:12','2025-10-24 12:14:28'),(40,'Lasleen',NULL,'active','2025-03-30 11:24:07','2025-04-05 09:36:28'),(41,'Electronics',NULL,'active','2025-04-09 12:49:59','2025-04-09 12:49:59'),(42,'testinggagag',NULL,'active','2025-04-15 11:20:21','2025-10-24 13:47:32'),(43,'a new ones',NULL,'active','2025-08-28 11:34:03','2025-10-31 11:48:22'),(44,'anada one',NULL,'inactive','2025-08-28 12:57:32','2025-10-22 12:07:59'),(45,'mike test',NULL,'inactive','2025-10-18 12:37:20','2025-10-22 11:32:49'),(46,'Vet',NULL,'inactive','2025-10-22 11:50:59','2025-10-22 11:55:38'),(47,'Testing Again',NULL,'active','2025-10-24 13:26:40','2025-10-24 13:27:08');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discounts`
--

LOCK TABLES `discounts` WRITE;
/*!40000 ALTER TABLE `discounts` DISABLE KEYS */;
INSERT INTO `discounts` VALUES (1,'Senior',6.00,'active'),(2,'Disabled',5.00,'active'),(3,'N/A',0.00,'active');
/*!40000 ALTER TABLE `discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,'Salary','Keziah',500.00,'2025-03-31 16:00:00',NULL),(2,'Salary','Khloe',500.00,'2025-03-31 16:00:00',NULL),(3,'Salary','Kraven',500.00,'2025-03-31 16:00:00',NULL);
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `perishability` enum('Perishable','N/P') DEFAULT 'N/P',
  `stock` int(11) NOT NULL,
  `unit` varchar(11) NOT NULL,
  `buy_price` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry` text NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `is_archived` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'850040427066','Prime Ice Pop 500ml','Beverages','N/P',48,'Piece',200.00,220.00,'2025-03-27 02:48:11','2026-02-28','active',0),(2,'4800361331500','BearBrand Sterelized 200ml','Beverages','N/P',100,'Piece',25.00,30.00,'2025-03-27 02:51:42','04-30-2025','active',0),(3,'4800067130063','Philusa Ethyl Alcohol 70% 350ml','Household Supplies','N/P',50,'Piece',80.00,90.00,'2025-03-27 04:09:46','04-30-2025','active',0),(4,'4809010508898','Milcu underarm&foot 40g','Household Supplies','N/P',50,'Piece',50.00,60.00,'2025-03-27 04:45:57','2039-09-29','active',0),(6,'4806524039887','Luxe Organix Aloe Vera 100ml','Household Supplies','N/P',100,'Piece',180.00,195.00,'2025-03-28 05:39:40','2029-10-15','active',0),(10,'6974944461538','Telesin Gopro battery HERO8/7/6','Electronics','N/P',2133,'Piece',90.00,100.00,'2025-03-29 19:38:40','2025-09-30','active',0),(16,'7622202243639','Dairy Milk - Milk Chocolate 11g','Snacks','N/P',22,'Piece',25.00,30.00,'2025-04-03 07:17:58','2025-05-07','active',0),(17,'4800014141081','Summit 500ml','Beverages','N/P',1355,'Piece',15.00,18.00,'2025-04-04 05:51:49','2025-04-30','active',0),(20,'8885007027876','Epson 003 ink 65ml','Household Supplies','N/P',56,'Piece',100.00,200.00,'2025-04-04 22:49:46','2027-10-26','active',0),(21,'Z11M-GA49V11-2502000537','YESTON RX6600 8GB','Electronics','N/P',204,'Piece',12500.00,13000.00,'2025-04-05 00:22:18','2025-12-30','active',0),(24,'4800758039887','testlang1','Milk','N/P',55,'Piece',70.00,79.00,'0000-00-00 00:00:00','2026-01-31','archived',1),(25,'23424637132435','veggies','Fruits ; Vegetables','N/P',55,'Piece',500.00,50.00,'0000-00-00 00:00:00','2025-08-09','active',1),(27,'123738493','tomato','Fruits ; Vegetables','N/P',49,'Kg',60.00,65.00,'2025-10-18 14:29:33','2025-12-31','active',0),(28,'4124125123','Scavon Vet Cream 50g','Medical','N/P',27,'Piece',100.00,110.00,'2025-10-22 11:48:35','2029-11-23','active',0),(29,'5123123','tipalia','Fruits ; Vegetables','Perishable',1043,'Kg',200.00,220.00,'2025-10-23 14:05:53','2025-10-31','active',0),(30,'4800131591004','Biogenic 70% 330ml','Household Supplies','N/P',32,'Piece',60.00,65.00,'2025-10-24 12:04:16','2034-01-24','archived',0),(31,'426672324123561','another test','Beverages','N/P',3,'Piece',100.00,110.00,'2025-10-24 12:33:41','2025-10-31','active',0),(34,'45611515514','adasda','Meat & Seafood','N/P',123,'Kg',213.00,123.00,'2025-10-24 07:07:36','2025-12-31','archived',0),(35,'14115123','31fasf','Dairy & Eggs','N/P',12314,'Kg',1235.00,12344.00,'2025-10-24 07:08:10','2025-12-31','archived',0),(36,'124516161613451','TESTING','Dairy & Eggs','Perishable',51,'Piece',123.00,1234.00,'2025-10-24 13:10:11','2025-12-25','active',0),(38,'4800131591004','Biogenic 70% 330mll','Household Supplies','N/P',50,'Piece',55.00,60.00,'2025-10-24 13:40:35','2026-10-24','active',0);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `cash_given` decimal(10,2) DEFAULT NULL,
  `change_amount` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'Completed',
  `return_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (27,3,47.50,'2025-09-04 19:54:06','gcash',50.00,7.50,5.00,'Returned',NULL),(28,3,95.00,'2025-09-04 19:54:27','gcash',95.00,5.00,5.00,'Returned',NULL),(29,3,450.00,'2025-09-04 20:00:50','cash',500.00,50.00,0.00,'Returned','just just'),(30,3,28.50,'2025-09-04 20:03:10','cash',30.00,6.50,5.00,'Returned',NULL),(31,1,342.00,'2025-10-12 21:46:32','cash',500.00,163.00,5.00,'Returned',NULL),(35,1,1045.00,'2025-10-13 19:57:57','cash',1500.00,460.00,5.00,'Returned',NULL),(36,1,800.00,'2025-10-13 19:59:09','cash',1000.00,200.00,0.00,'Returned',NULL),(37,1,380.00,'2025-10-15 20:52:18','cash',400.00,25.00,5.00,'Returned',NULL),(38,1,34.20,'2025-10-15 22:06:38','cash',40.00,10.80,5.00,'Returned',NULL),(39,1,18.00,'2025-10-15 22:08:41','gcash',20.00,2.00,0.00,'Completed',NULL),(40,1,26000.00,'2025-10-18 20:35:30','cash',25000.00,-1000.00,0.00,'Returned',NULL),(41,4,190.00,'2025-10-18 21:43:26','cash',190.00,5.00,5.00,'Completed',NULL),(42,4,18.00,'2025-10-18 22:15:13','cash',20.00,2.00,0.00,'Completed',NULL),(43,4,30.00,'2025-10-18 22:26:45','cash',50.00,20.00,0.00,'Completed',NULL),(44,1,13000.00,'2025-10-22 18:58:27','cash',13000.00,0.00,0.00,'Completed',NULL),(45,1,190.00,'2025-10-22 19:14:00','cash',200.00,15.00,5.00,'Completed',NULL),(46,1,12350.00,'2025-10-22 19:22:36','cash',12350.00,5.00,5.00,'Returned',NULL),(47,1,37050.00,'2025-10-22 19:26:21','cash',38000.00,955.00,5.00,'Returned','deffective'),(48,1,13148.00,'2025-10-22 19:28:44','cash',13200.00,52.00,0.00,'Completed',NULL),(49,1,74100.00,'2025-10-22 19:35:30','cash',75000.00,905.00,5.00,'Completed',NULL),(50,1,2200.00,'2025-10-22 20:13:56','gcash',0.00,-2200.00,0.00,'Completed',NULL),(51,1,37050.00,'2025-10-23 21:55:59','gcash',0.00,-37045.00,5.00,'Completed',NULL),(52,1,220.00,'2025-10-23 22:07:11','cash',250.00,30.00,0.00,'Completed',NULL),(53,1,97.50,'2025-10-23 22:49:05','cash',100.00,2.50,0.00,'Completed',NULL),(54,1,387.20,'2025-10-23 22:51:29','cash',390.00,2.80,0.00,'Completed',NULL),(55,1,522.50,'2025-10-24 19:58:20','cash',550.00,32.50,5.00,'Completed',NULL),(56,1,367.40,'2025-10-24 20:18:30','cash',170.00,-197.40,0.00,'Completed',NULL),(57,1,313.50,'2025-10-24 20:22:42','cash',0.00,-308.50,5.00,'Completed',NULL),(58,1,110.00,'2025-10-24 20:23:30','cash',120.00,10.00,0.00,'Completed',NULL),(59,6,325.00,'2025-10-24 21:23:21','cash',500.00,175.00,0.00,'Completed',NULL),(60,7,1599.00,'2025-10-24 21:44:06','cash',2000.00,401.00,0.00,'Completed',NULL);
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_items`
--

DROP TABLE IF EXISTS `sales_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `cash_given` decimal(10,2) DEFAULT NULL,
  `change_amount` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `status` enum('Sold','Returned') DEFAULT 'Sold',
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_items`
--

LOCK TABLES `sales_items` WRITE;
/*!40000 ALTER TABLE `sales_items` DISABLE KEYS */;
INSERT INTO `sales_items` VALUES (7,27,'23424637132435',1.00,50.00,'gcash','123123551231',50.00,2.50,-47.50,5.00,'Sold'),(8,28,'6974944461538',1.00,100.00,'gcash','123124512312',95.00,0.00,-95.00,5.00,'Sold'),(9,29,'8885007027876',1.00,200.00,'cash','',450.00,500.00,50.00,0.00,'Returned'),(10,29,'7622202243639',1.00,30.00,'cash','',450.00,500.00,50.00,0.00,'Returned'),(11,29,'850040427066',1.00,220.00,'cash','',450.00,500.00,50.00,0.00,'Returned'),(12,30,'4800361331500',1.00,30.00,'cash','',28.50,30.00,1.50,5.00,'Sold'),(13,31,'4800014141081',20.00,18.00,'cash','',342.00,500.00,158.00,5.00,'Sold'),(14,35,'8885007027876',5.00,200.00,'cash','',1045.00,1500.00,455.00,5.00,'Sold'),(15,35,'6974944461538',1.00,100.00,'cash','',1045.00,1500.00,455.00,5.00,'Sold'),(16,36,'8885007027876',4.00,200.00,'cash','',800.00,1000.00,200.00,0.00,'Sold'),(17,37,'8885007027876',2.00,200.00,'cash','',380.00,400.00,20.00,5.00,'Sold'),(18,38,'4800014141081',2.00,18.00,'cash','',34.20,40.00,5.80,5.00,'Sold'),(19,39,'4800014141081',1.00,18.00,'gcash','2342357345',18.00,20.00,2.00,0.00,'Sold'),(20,40,'Z11M-GA49V11-2502000537',2.00,13000.00,'cash','',26000.00,25000.00,-1000.00,0.00,'Returned'),(21,41,'8885007027876',1.00,200.00,'cash','',190.00,190.00,0.00,5.00,'Sold'),(22,42,'4800014141081',1.00,18.00,'cash','',18.00,20.00,2.00,0.00,'Sold'),(23,43,'7622202243639',1.00,30.00,'cash','',30.00,50.00,20.00,0.00,'Sold'),(24,44,'Z11M-GA49V11-2502000537',1.00,13000.00,'cash','',13000.00,13000.00,0.00,0.00,'Sold'),(25,45,'23424637132435',4.00,50.00,'cash','',190.00,200.00,10.00,5.00,'Sold'),(26,46,'Z11M-GA49V11-2502000537',1.00,13000.00,'cash','',12350.00,12350.00,0.00,5.00,'Sold'),(27,47,'Z11M-GA49V11-2502000537',3.00,13000.00,'cash','',37050.00,38000.00,950.00,5.00,'Returned'),(28,48,'4800014141081',1.00,18.00,'cash','',13148.00,13200.00,52.00,0.00,'Sold'),(29,48,'7622202243639',1.00,30.00,'cash','',13148.00,13200.00,52.00,0.00,'Sold'),(30,48,'Z11M-GA49V11-2502000537',1.00,13000.00,'cash','',13148.00,13200.00,52.00,0.00,'Sold'),(31,48,'6974944461538',1.00,100.00,'cash','',13148.00,13200.00,52.00,0.00,'Returned'),(32,49,'Z11M-GA49V11-2502000537',6.00,13000.00,'cash','',74100.00,75000.00,900.00,5.00,'Sold'),(33,50,'4124125123',20.00,110.00,'gcash','1312561234123',2200.00,0.00,-2200.00,0.00,'Sold'),(34,51,'Z11M-GA49V11-2502000537',3.00,13000.00,'gcash','1231462342346',37050.00,0.00,-37050.00,5.00,'Sold'),(35,52,'5123123',1.00,220.00,'cash','',220.00,250.00,30.00,0.00,'Sold'),(36,53,'123738493',1.00,65.00,'cash','',97.50,100.00,2.50,0.00,'Sold'),(37,54,'5123123',1.00,220.00,'cash','',387.20,390.00,2.80,0.00,'Sold'),(38,55,'4124125123',5.00,110.00,'cash','',522.50,550.00,27.50,5.00,'Sold'),(39,56,'5123123',1.00,220.00,'cash','',367.40,170.00,-197.40,0.00,'Sold'),(40,57,'5123123',1.00,220.00,'cash','',313.50,0.00,-313.50,5.00,'Sold'),(41,58,'4124125123',1.00,110.00,'cash','',110.00,120.00,10.00,0.00,'Sold'),(42,59,'4800131591004',5.00,65.00,'cash','',325.00,500.00,175.00,0.00,'Sold'),(43,60,'4800131591004',5.00,60.00,'cash','',1599.00,2000.00,401.00,0.00,'Sold'),(44,60,'124516161613451',1.00,1234.00,'cash','',1599.00,2000.00,401.00,0.00,'Sold'),(45,60,'123738493',1.00,65.00,'cash','',1599.00,2000.00,401.00,0.00,'Sold');
/*!40000 ALTER TABLE `sales_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','seller','cashier') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Rutty','Joshua','SHUWAWA','$2y$10$DT14CHxeuOp7krG.8aogVOY5./6yW92z8lJBn7Cw3wTkYd1xIjb3y','admin','Active'),(2,'Kevin','Kevin','KVN','$2y$10$ffnHTuSXUogI9W6tIyo6iOmwSxvh8YJCbkoTHllIKcmdIxL4ctEzO','cashier','Active'),(3,'kevs','kevs','kevss','$2y$10$5WfSO2TzATMeCNNoFtmDfumpjHj6gXflNrrPl4i0X6hySJnLlx0l2','admin','Active'),(4,'arrah','maye','pass','$2y$10$y0YwKwSDeUN4EjZY4xkKjeYSXZukmULyzCAX4NBmFoe057RglkeV.','admin','Active'),(5,'ngibin','sats','ngebs','$2y$10$rSGqvDl1lxaEL9ueArQHCebkEi57A8ozm6VliUvHhvWblA5CsfWZG','cashier','Inactive'),(6,'Kevin','Satur','Kevs','$2y$10$eTS/YN3U75r5.Ej5oOhhUO.Sfo4okABJ6QtjYYCZS6t76Y3in.jwK','admin','Active'),(7,'Kevin','Sats','Kebs','$2y$10$33G/USTwFYjn.Gq46/otCepxt7loKO1lLwsvOymdUFXq2MfqVK1Di','admin','Active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-07 23:16:17
