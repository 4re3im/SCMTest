-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: cap
-- ------------------------------------------------------
-- Server version 5.7.22

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
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `pretty_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `biography` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authors_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_codes`
--

DROP TABLE IF EXISTS `brand_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brand_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand_codes_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `cap_schema`
--

DROP TABLE IF EXISTS `cap_schema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cap_schema` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `batch` int(11) DEFAULT NULL,
  `migration_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cap_schema`
--

LOCK TABLES `cap_schema` WRITE;
/*!40000 ALTER TABLE `cap_schema` DISABLE KEYS */;
INSERT INTO `cap_schema` VALUES (1,'1503250034279_user',1,'2019-06-26 11:37:49'),(2,'1503250034280_token',1,'2019-06-26 11:37:50'),(3,'1559105901170_formats_schema',1,'2019-06-26 11:37:51'),(4,'1559107633702_countries_schema',1,'2019-06-26 11:37:52'),(5,'1559107633703_states_schema',1,'2019-06-26 11:37:52'),(6,'1559107635704_subjects_schema',1,'2019-06-26 11:37:54'),(7,'1559109912456_content_access_schema',1,'2019-06-26 11:37:54'),(8,'1559109924011_year_levels_schema',1,'2019-06-26 11:37:54'),(9,'1559109935721_authors_schema',1,'2019-06-26 11:37:55'),(10,'1559109935722_divisions_schema',1,'2019-06-26 11:37:55'),(11,'1559109935723_editions_schema',1,'2019-06-26 11:37:55'),(12,'1559109935725_types_schema',1,'2019-06-26 11:37:56'),(13,'1559109935727_brand_codes_schema',1,'2019-06-26 11:37:56'),(14,'1559112024777_series_schema',1,'2019-06-26 11:37:56'),(15,'1559113319874_series_formats_schema',1,'2019-06-26 11:37:57'),(16,'1559113319875_series_subjects_schema',1,'2019-06-26 11:37:59'),(17,'1559113319876_series_states_schema',1,'2019-06-26 11:38:00'),(18,'1559113319877_series_divisions_schema',1,'2019-06-26 11:38:01'),(19,'1559113319878_series_year_levels_schema',1,'2019-06-26 11:38:02'),(20,'1559113906760_titles_schema',1,'2019-06-26 11:38:03'),(21,'1559117293996_series_reviews_schema',1,'2019-06-26 11:38:04'),(22,'1559117293997_title_reviews_schema',1,'2019-06-26 11:38:04'),(23,'1559117293998_content_folders_schema',1,'2019-06-26 11:38:05'),(24,'1559118849532_contents_schema',1,'2019-06-26 11:38:05'),(25,'1559119545646_content_details_schema',1,'2019-06-26 11:38:06'),(26,'1559119545657_tabs_schema',1,'2019-06-26 11:38:08'),(27,'1559119545689_tab_details_schema',1,'2019-06-26 11:38:08'),(28,'1559119545690_tab_hm_ids_schema',1,'2019-06-26 11:38:09'),(29,'1559119545691_tab_contents_schema',1,'2019-06-26 11:38:10'),(30,'1559119545692_title_authors_schema',1,'2019-06-26 11:38:11'),(31,'1559119545693_title_states_schema',1,'2019-06-26 11:38:12'),(32,'1559119545694_title_divisions_schema',1,'2019-06-26 11:38:13'),(33,'1559119545695_title_year_levels_schema',1,'2019-06-26 11:38:15'),(34,'1559119545696_title_formats_schema',1,'2019-06-26 11:38:16'),(35,'1559119545697_title_related_titles_schema',1,'2019-06-26 11:38:17'),(36,'1559119545698_title_sample_pages_schema',1,'2019-06-26 11:38:18'),(37,'1559119545699_title_subjects_schema',1,'2019-06-26 11:38:19'),(38,'1559119545700_title_brand_codes_schema',1,'2019-06-26 11:38:20'),(39,'1559119545702_content_detail_types_schema',1,'2019-06-26 11:38:22'),(40,'1559119545703_content_types_schema',1,'2019-06-26 11:38:23');
/*!40000 ALTER TABLE `cap_schema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_access`
--

DROP TABLE IF EXISTS `content_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_access_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_detail_types`
--

DROP TABLE IF EXISTS `content_detail_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_detail_types` (
  `content_detail_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  KEY `content_detail_types_content_detail_id_foreign` (`content_detail_id`),
  KEY `content_detail_types_type_id_foreign` (`type_id`),
  CONSTRAINT `content_detail_types_content_detail_id_foreign` FOREIGN KEY (`content_detail_id`) REFERENCES `content_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `content_detail_types_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_details`
--

DROP TABLE IF EXISTS `content_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) unsigned NOT NULL,
  `name` text COLLATE utf8_unicode_ci,
  `note` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `sort_order` int(3) unsigned NOT NULL,
  `url` text COLLATE utf8_unicode_ci,
  `window_behaviour` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `window_height` int(10) DEFAULT NULL,
  `window_width` int(10) DEFAULT NULL,
  `html_content` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `is_demo_only` tinyint(1) NOT NULL DEFAULT '1',
  `file_name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_path` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_file_size_shown` tinyint(1) NOT NULL DEFAULT '0',
  `file_size` bigint(20) DEFAULT NULL,
  `file_uploaded_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `content_details_content_id_foreign` (`content_id`),
  CONSTRAINT `content_details_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_folders`
--

DROP TABLE IF EXISTS `content_folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_folders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `content_folders_title_id_foreign` (`title_id`),
  CONSTRAINT `content_folders_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_types`
--

DROP TABLE IF EXISTS `content_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_types` (
  `content_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  KEY `content_types_content_id_foreign` (`content_id`),
  KEY `content_types_type_id_foreign` (`type_id`),
  CONSTRAINT `content_types_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `content_types_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `heading` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `details` text COLLATE utf8_unicode_ci,
  `note` text COLLATE utf8_unicode_ci,
  `is_global` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `contents_folder_id_foreign` (`folder_id`),
  CONSTRAINT `contents_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `content_folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `iso_code` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `divisions`
--

DROP TABLE IF EXISTS `divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divisions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `divisions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `editions`
--

DROP TABLE IF EXISTS `editions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `editions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formats`
--

DROP TABLE IF EXISTS `formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `pretty_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_digital` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `formats_name_unique` (`name`),
  UNIQUE KEY `formats_pretty_url_unique` (`pretty_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series`
--

DROP TABLE IF EXISTS `series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `series_number` int(10) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `pretty_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `tagline` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series_divisions`
--

DROP TABLE IF EXISTS `series_divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series_divisions` (
  `series_id` int(10) unsigned NOT NULL,
  `division_id` int(10) unsigned NOT NULL,
  KEY `series_divisions_series_id_foreign` (`series_id`),
  KEY `series_divisions_division_id_foreign` (`division_id`),
  CONSTRAINT `series_divisions_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `series_divisions_series_id_foreign` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series_formats`
--

DROP TABLE IF EXISTS `series_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series_formats` (
  `series_id` int(10) unsigned NOT NULL,
  `format_id` int(10) unsigned NOT NULL,
  KEY `series_formats_series_id_foreign` (`series_id`),
  KEY `series_formats_format_id_foreign` (`format_id`),
  CONSTRAINT `series_formats_format_id_foreign` FOREIGN KEY (`format_id`) REFERENCES `formats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `series_formats_series_id_foreign` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series_reviews`
--

DROP TABLE IF EXISTS `series_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` int(10) unsigned NOT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `series_reviews_series_id_foreign` (`series_id`),
  CONSTRAINT `series_reviews_series_id_foreign` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series_states`
--

DROP TABLE IF EXISTS `series_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series_states` (
  `series_id` int(10) unsigned NOT NULL,
  `state_id` int(10) unsigned NOT NULL,
  KEY `series_states_series_id_foreign` (`series_id`),
  KEY `series_states_state_id_foreign` (`state_id`),
  CONSTRAINT `series_states_series_id_foreign` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `series_states_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series_subjects`
--

DROP TABLE IF EXISTS `series_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series_subjects` (
  `series_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  KEY `series_subjects_series_id_foreign` (`series_id`),
  KEY `series_subjects_subject_id_foreign` (`subject_id`),
  CONSTRAINT `series_subjects_series_id_foreign` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `series_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series_year_levels`
--

DROP TABLE IF EXISTS `series_year_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series_year_levels` (
  `series_id` int(10) unsigned NOT NULL,
  `year_level_id` int(10) unsigned DEFAULT NULL,
  KEY `series_year_levels_series_id_foreign` (`series_id`),
  KEY `series_year_levels_year_level_id_foreign` (`year_level_id`),
  CONSTRAINT `series_year_levels_series_id_foreign` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `series_year_levels_year_level_id_foreign` FOREIGN KEY (`year_level_id`) REFERENCES `year_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `states_name_unique` (`name`),
  KEY `states_country_id_foreign` (`country_id`),
  CONSTRAINT `states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `pretty_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_secondary` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_name_unique` (`name`),
  UNIQUE KEY `subjects_pretty_url_unique` (`pretty_url`),
  KEY `subjects_country_id_foreign` (`country_id`),
  CONSTRAINT `subjects_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tab_contents`
--

DROP TABLE IF EXISTS `tab_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_contents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tab_id` int(10) unsigned NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_demo_only` tinyint(1) NOT NULL DEFAULT '1',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `column_number` int(3) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tab_contents_tab_id_foreign` (`tab_id`),
  KEY `tab_contents_content_id_foreign` (`content_id`),
  CONSTRAINT `tab_contents_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tab_contents_tab_id_foreign` FOREIGN KEY (`tab_id`) REFERENCES `tabs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tab_details`
--

DROP TABLE IF EXISTS `tab_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tab_id` int(10) unsigned NOT NULL,
  `sort_order` int(3) unsigned NOT NULL,
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Tab',
  `catalogue_field` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_content_visibility_open` tinyint(1) NOT NULL DEFAULT '0',
  `content_type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tab_details_tab_id_foreign` (`tab_id`),
  CONSTRAINT `tab_details_tab_id_foreign` FOREIGN KEY (`tab_id`) REFERENCES `tabs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tab_hm_ids`
--

DROP TABLE IF EXISTS `tab_hm_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_hm_ids` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tab_id` int(10) unsigned NOT NULL,
  `entitlement_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hm_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tab_hm_ids_tab_id_foreign` (`tab_id`),
  CONSTRAINT `tab_hm_ids_tab_id_foreign` FOREIGN KEY (`tab_id`) REFERENCES `tabs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tabs`
--

DROP TABLE IF EXISTS `tabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tabs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `content_access_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `public_text` text COLLATE utf8_unicode_ci,
  `private_text` text COLLATE utf8_unicode_ci,
  `custom_access_message` text COLLATE utf8_unicode_ci,
  `column_number` int(3) DEFAULT NULL,
  `resource_url` text COLLATE utf8_unicode_ci,
  `icon` int(3) DEFAULT NULL,
  `user_type_restriction` int(2) DEFAULT '1',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_public_text_used` tinyint(1) NOT NULL DEFAULT '0',
  `is_my_resources_link` tinyint(1) NOT NULL DEFAULT '0',
  `is_elevate_product` tinyint(1) NOT NULL DEFAULT '0',
  `is_hm_product` tinyint(1) NOT NULL DEFAULT '0',
  `sample_hm_id` int(11) DEFAULT NULL,
  `sample_hm_prod_url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tabs_title_id_foreign` (`title_id`),
  KEY `tabs_content_access_id_foreign` (`content_access_id`),
  CONSTRAINT `tabs_content_access_id_foreign` FOREIGN KEY (`content_access_id`) REFERENCES `content_access` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tabs_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_authors`
--

DROP TABLE IF EXISTS `title_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_authors` (
  `title_id` int(10) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  KEY `title_authors_title_id_foreign` (`title_id`),
  KEY `title_authors_author_id_foreign` (`author_id`),
  CONSTRAINT `title_authors_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_authors_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_brand_codes`
--

DROP TABLE IF EXISTS `title_brand_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_brand_codes` (
  `title_id` int(10) unsigned NOT NULL,
  `brand_code_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `title_brand_codes_brand_code_id_unique` (`brand_code_id`),
  KEY `title_brand_codes_title_id_foreign` (`title_id`),
  CONSTRAINT `title_brand_codes_brand_code_id_foreign` FOREIGN KEY (`brand_code_id`) REFERENCES `brand_codes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_brand_codes_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
-- Table structure for table `title_divisions`
--

DROP TABLE IF EXISTS `title_divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_divisions` (
  `title_id` int(10) unsigned NOT NULL,
  `division_id` int(10) unsigned NOT NULL,
  KEY `title_divisions_title_id_foreign` (`title_id`),
  KEY `title_divisions_division_id_foreign` (`division_id`),
  CONSTRAINT `title_divisions_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_divisions_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_formats`
--

DROP TABLE IF EXISTS `title_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_formats` (
  `title_id` int(10) unsigned NOT NULL,
  `format_id` int(10) unsigned NOT NULL,
  KEY `title_formats_title_id_foreign` (`title_id`),
  KEY `title_formats_format_id_foreign` (`format_id`),
  CONSTRAINT `title_formats_format_id_foreign` FOREIGN KEY (`format_id`) REFERENCES `formats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_formats_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_related_titles`
--

DROP TABLE IF EXISTS `title_related_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_related_titles` (
  `title_id` int(10) unsigned NOT NULL,
  `related_title_id` int(10) unsigned NOT NULL,
  KEY `title_related_titles_title_id_foreign` (`title_id`),
  KEY `title_related_titles_related_title_id_foreign` (`related_title_id`),
  CONSTRAINT `title_related_titles_related_title_id_foreign` FOREIGN KEY (`related_title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_related_titles_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_reviews`
--

DROP TABLE IF EXISTS `title_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `title_reviews_title_id_foreign` (`title_id`),
  CONSTRAINT `title_reviews_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_sample_pages`
--

DROP TABLE IF EXISTS `title_sample_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_sample_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_page_proof` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `title_sample_pages_title_id_foreign` (`title_id`),
  CONSTRAINT `title_sample_pages_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_states`
--

DROP TABLE IF EXISTS `title_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_states` (
  `title_id` int(10) unsigned NOT NULL,
  `state_id` int(10) unsigned NOT NULL,
  KEY `title_states_title_id_foreign` (`title_id`),
  KEY `title_states_state_id_foreign` (`state_id`),
  CONSTRAINT `title_states_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_states_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_subjects`
--

DROP TABLE IF EXISTS `title_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_subjects` (
  `title_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  KEY `title_subjects_title_id_foreign` (`title_id`),
  KEY `title_subjects_subject_id_foreign` (`subject_id`),
  CONSTRAINT `title_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_subjects_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `title_supporting_titles`
--

DROP TABLE IF EXISTS `title_supporting_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_supporting_titles` (
  `title_id` int(10) unsigned NOT NULL,
  `supporting_title_id` int(10) unsigned NOT NULL,
  KEY `title_supporting_titles_title_id_foreign` (`title_id`),
  KEY `title_supporting_titles_supporting_title_id_foreign` (`supporting_title_id`),
  CONSTRAINT `title_supporting_titles_supporting_title_id_foreign` FOREIGN KEY (`supporting_title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_supporting_titles_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title_year_levels`
--

DROP TABLE IF EXISTS `title_year_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title_year_levels` (
  `title_id` int(10) unsigned NOT NULL,
  `year_level_id` int(10) unsigned DEFAULT NULL,
  KEY `title_year_levels_title_id_foreign` (`title_id`),
  KEY `title_year_levels_year_level_id_foreign` (`year_level_id`),
  CONSTRAINT `title_year_levels_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `title_year_levels_year_level_id_foreign` FOREIGN KEY (`year_level_id`) REFERENCES `year_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `titles`
--

DROP TABLE IF EXISTS `titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `titles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` int(10) unsigned DEFAULT NULL,
  `edition_id` int(10) unsigned DEFAULT NULL,
  `isbn_13` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `isbn_10` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `pretty_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `subtitle` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `feature` text COLLATE utf8_unicode_ci,
  `availability` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tagline` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_buy_now_shown` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `titles_series_id_foreign` (`series_id`),
  KEY `titles_edition_id_foreign` (`edition_id`),
  CONSTRAINT `titles_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `titles_series_id_foreign` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `type` varchar(80) NOT NULL,
  `is_revoked` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tokens_token_unique` (`token`),
  KEY `tokens_user_id_foreign` (`user_id`),
  KEY `tokens_token_index` (`token`),
  CONSTRAINT `tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password` varchar(60) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `year_levels`
--

DROP TABLE IF EXISTS `year_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `year_levels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `year_levels_level_unique` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-06-26 19:41:02
