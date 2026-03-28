-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: jadiumrah
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

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
-- Table structure for table `agent_tiers`
--

DROP TABLE IF EXISTS `agent_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agent_tiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_closing` int NOT NULL,
  `max_closing` int DEFAULT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agent_tiers_branch_id_foreign` (`branch_id`),
  CONSTRAINT `agent_tiers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent_tiers`
--

LOCK TABLES `agent_tiers` WRITE;
/*!40000 ALTER TABLE `agent_tiers` DISABLE KEYS */;
/*!40000 ALTER TABLE `agent_tiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_agent` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agents_kode_agent_unique` (`kode_agent`),
  UNIQUE KEY `agents_slug_unique` (`slug`),
  KEY `agents_user_id_foreign` (`user_id`),
  KEY `agents_branch_id_foreign` (`branch_id`),
  CONSTRAINT `agents_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `agents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agents`
--

LOCK TABLES `agents` WRITE;
/*!40000 ALTER TABLE `agents` DISABLE KEYS */;
/*!40000 ALTER TABLE `agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_addons`
--

DROP TABLE IF EXISTS `booking_addons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_addons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `marketing_addon_id` bigint unsigned NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `price` decimal(15,2) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_addons_booking_id_marketing_addon_id_unique` (`booking_id`,`marketing_addon_id`),
  KEY `booking_addons_marketing_addon_id_foreign` (`marketing_addon_id`),
  CONSTRAINT `booking_addons_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_addons_marketing_addon_id_foreign` FOREIGN KEY (`marketing_addon_id`) REFERENCES `marketing_addons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_addons`
--

LOCK TABLES `booking_addons` WRITE;
/*!40000 ALTER TABLE `booking_addons` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_addons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_jamaah`
--

DROP TABLE IF EXISTS `booking_jamaah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_jamaah` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `jamaah_id` bigint unsigned NOT NULL,
  `room_type` enum('double','triple','quad') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `seat_number` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_jamaah_booking_id_foreign` (`booking_id`),
  KEY `booking_jamaah_jamaah_id_foreign` (`jamaah_id`),
  CONSTRAINT `booking_jamaah_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_jamaah_jamaah_id_foreign` FOREIGN KEY (`jamaah_id`) REFERENCES `jamaahs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_jamaah`
--

LOCK TABLES `booking_jamaah` WRITE;
/*!40000 ALTER TABLE `booking_jamaah` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_jamaah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_locks`
--

DROP TABLE IF EXISTS `booking_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_locks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_departure_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `qty` int unsigned NOT NULL,
  `expired_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_locks_paket_departure_id_expired_at_index` (`paket_departure_id`,`expired_at`),
  KEY `booking_locks_user_id_index` (`user_id`),
  CONSTRAINT `booking_locks_paket_departure_id_foreign` FOREIGN KEY (`paket_departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_locks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_locks`
--

LOCK TABLES `booking_locks` WRITE;
/*!40000 ALTER TABLE `booking_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paket_id` bigint unsigned NOT NULL,
  `marketing_campaign_id` bigint unsigned DEFAULT NULL,
  `paket_departure_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `agent_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `room_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `price_per_person_snapshot` decimal(15,2) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','waiting_payment','partial_paid','confirmed','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `voucher_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `original_price_snapshot` decimal(15,2) DEFAULT NULL,
  `discount_snapshot` decimal(15,2) DEFAULT NULL,
  `promo_label_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_booking_code_unique` (`booking_code`),
  UNIQUE KEY `bookings_invoice_number_unique` (`invoice_number`),
  KEY `bookings_paket_id_foreign` (`paket_id`),
  KEY `bookings_paket_departure_id_foreign` (`paket_departure_id`),
  KEY `bookings_branch_id_foreign` (`branch_id`),
  KEY `bookings_marketing_campaign_id_foreign` (`marketing_campaign_id`),
  KEY `bookings_voucher_id_foreign` (`voucher_id`),
  KEY `bookings_status_expired_at_index` (`status`,`expired_at`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_created_by_foreign` (`created_by`),
  KEY `bookings_agent_id_foreign` (`agent_id`),
  CONSTRAINT `bookings_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_marketing_campaign_id_foreign` FOREIGN KEY (`marketing_campaign_id`) REFERENCES `marketing_campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_paket_departure_id_foreign` FOREIGN KEY (`paket_departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_code_unique` (`code`),
  KEY `branches_code_index` (`code`),
  KEY `branches_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Head Office','HQ','Jakarta',NULL,NULL,1,'2026-03-27 16:34:18','2026-03-27 16:34:18');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_paket`
--

DROP TABLE IF EXISTS `campaign_paket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_paket` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `marketing_campaign_id` bigint unsigned NOT NULL,
  `paket_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_paket_marketing_campaign_id_paket_id_unique` (`marketing_campaign_id`,`paket_id`),
  KEY `campaign_paket_paket_id_foreign` (`paket_id`),
  CONSTRAINT `campaign_paket_marketing_campaign_id_foreign` FOREIGN KEY (`marketing_campaign_id`) REFERENCES `marketing_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaign_paket_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_paket`
--

LOCK TABLES `campaign_paket` WRITE;
/*!40000 ALTER TABLE `campaign_paket` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_paket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code_counters`
--

DROP TABLE IF EXISTS `code_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `code_counters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_number` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_counters_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code_counters`
--

LOCK TABLES `code_counters` WRITE;
/*!40000 ALTER TABLE `code_counters` DISABLE KEYS */;
/*!40000 ALTER TABLE `code_counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_branch_rules`
--

DROP TABLE IF EXISTS `commission_branch_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_branch_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commission_scheme_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `paket_id` bigint unsigned DEFAULT NULL,
  `agent_percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commission_branch_rules_commission_scheme_id_foreign` (`commission_scheme_id`),
  KEY `commission_branch_rules_branch_id_foreign` (`branch_id`),
  KEY `commission_branch_rules_paket_id_foreign` (`paket_id`),
  CONSTRAINT `commission_branch_rules_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_branch_rules_commission_scheme_id_foreign` FOREIGN KEY (`commission_scheme_id`) REFERENCES `commission_schemes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_branch_rules_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_branch_rules`
--

LOCK TABLES `commission_branch_rules` WRITE;
/*!40000 ALTER TABLE `commission_branch_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_branch_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_company_rules`
--

DROP TABLE IF EXISTS `commission_company_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_company_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commission_scheme_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `paket_id` bigint unsigned DEFAULT NULL,
  `amount_per_closing` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commission_company_rules_commission_scheme_id_foreign` (`commission_scheme_id`),
  KEY `commission_company_rules_branch_id_foreign` (`branch_id`),
  KEY `commission_company_rules_paket_id_foreign` (`paket_id`),
  CONSTRAINT `commission_company_rules_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_company_rules_commission_scheme_id_foreign` FOREIGN KEY (`commission_scheme_id`) REFERENCES `commission_schemes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_company_rules_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_company_rules`
--

LOCK TABLES `commission_company_rules` WRITE;
/*!40000 ALTER TABLE `commission_company_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_company_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_logs`
--

DROP TABLE IF EXISTS `commission_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `agent_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `commission_scheme_id` bigint unsigned NOT NULL,
  `company_amount` decimal(15,2) NOT NULL,
  `branch_amount` decimal(15,2) NOT NULL,
  `agent_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jamaah_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_logs_booking_jamaah_unique` (`booking_id`,`jamaah_id`),
  KEY `commission_logs_branch_id_foreign` (`branch_id`),
  KEY `commission_logs_commission_scheme_id_foreign` (`commission_scheme_id`),
  KEY `commission_logs_jamaah_id_foreign` (`jamaah_id`),
  KEY `commission_logs_agent_id_foreign` (`agent_id`),
  CONSTRAINT `commission_logs_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_logs_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_logs_commission_scheme_id_foreign` FOREIGN KEY (`commission_scheme_id`) REFERENCES `commission_schemes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_logs_jamaah_id_foreign` FOREIGN KEY (`jamaah_id`) REFERENCES `jamaahs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_logs`
--

LOCK TABLES `commission_logs` WRITE;
/*!40000 ALTER TABLE `commission_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_payout_items`
--

DROP TABLE IF EXISTS `commission_payout_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_payout_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commission_payout_id` bigint unsigned NOT NULL,
  `commission_log_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payout_log_unique` (`commission_payout_id`,`commission_log_id`),
  KEY `commission_payout_items_commission_log_id_foreign` (`commission_log_id`),
  CONSTRAINT `commission_payout_items_commission_log_id_foreign` FOREIGN KEY (`commission_log_id`) REFERENCES `commission_logs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_payout_items_commission_payout_id_foreign` FOREIGN KEY (`commission_payout_id`) REFERENCES `commission_payouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_payout_items`
--

LOCK TABLES `commission_payout_items` WRITE;
/*!40000 ALTER TABLE `commission_payout_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_payout_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_payouts`
--

DROP TABLE IF EXISTS `commission_payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_payouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payout_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('request','approved','paid','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'request',
  `created_by` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_by` bigint unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_payouts_payout_code_unique` (`payout_code`),
  KEY `commission_payouts_agent_id_foreign` (`agent_id`),
  KEY `commission_payouts_branch_id_foreign` (`branch_id`),
  KEY `commission_payouts_created_by_foreign` (`created_by`),
  KEY `commission_payouts_approved_by_foreign` (`approved_by`),
  KEY `commission_payouts_paid_by_foreign` (`paid_by`),
  CONSTRAINT `commission_payouts_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_payouts_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `commission_payouts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `commission_payouts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_payouts_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_payouts`
--

LOCK TABLES `commission_payouts` WRITE;
/*!40000 ALTER TABLE `commission_payouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_payouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_schemes`
--

DROP TABLE IF EXISTS `commission_schemes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_schemes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_schemes`
--

LOCK TABLES `commission_schemes` WRITE;
/*!40000 ALTER TABLE `commission_schemes` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_schemes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'Umrah Core','MAIN',1,'2026-03-27 16:34:18','2026-03-27 16:34:18');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_bank_accounts`
--

DROP TABLE IF EXISTS `company_bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_profile_id` bigint unsigned NOT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` enum('invoice','tabungan','refund','operational') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invoice',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_bank_accounts_company_profile_id_purpose_is_active_index` (`company_profile_id`,`purpose`,`is_active`),
  CONSTRAINT `company_bank_accounts_company_profile_id_foreign` FOREIGN KEY (`company_profile_id`) REFERENCES `company_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_bank_accounts`
--

LOCK TABLES `company_bank_accounts` WRITE;
/*!40000 ALTER TABLE `company_bank_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_menu`
--

DROP TABLE IF EXISTS `company_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `menu_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_menu_company_id_menu_id_unique` (`company_id`,`menu_id`),
  KEY `company_menu_menu_id_foreign` (`menu_id`),
  CONSTRAINT `company_menu_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_menu`
--

LOCK TABLES `company_menu` WRITE;
/*!40000 ALTER TABLE `company_menu` DISABLE KEYS */;
INSERT INTO `company_menu` VALUES (1,1,5,NULL,NULL),(2,1,6,NULL,NULL),(3,1,7,NULL,NULL),(4,1,8,NULL,NULL),(5,1,9,NULL,NULL),(6,1,10,NULL,NULL),(7,1,11,NULL,NULL),(8,1,12,NULL,NULL),(9,1,13,NULL,NULL),(10,1,14,NULL,NULL),(11,1,15,NULL,NULL),(12,1,16,NULL,NULL),(13,1,17,NULL,NULL),(14,1,18,NULL,NULL),(15,1,19,NULL,NULL),(16,1,20,NULL,NULL),(17,1,21,NULL,NULL),(18,1,22,NULL,NULL),(19,1,23,NULL,NULL),(20,1,24,NULL,NULL),(21,1,25,NULL,NULL),(22,1,26,NULL,NULL),(23,1,27,NULL,NULL),(24,1,28,NULL,NULL),(25,1,29,NULL,NULL),(26,1,30,NULL,NULL),(27,1,31,NULL,NULL),(28,1,32,NULL,NULL),(29,1,33,NULL,NULL),(30,1,34,NULL,NULL),(31,1,35,NULL,NULL),(32,1,36,NULL,NULL),(33,1,37,NULL,NULL),(34,1,38,NULL,NULL),(35,1,39,NULL,NULL),(36,1,40,NULL,NULL),(37,1,41,NULL,NULL),(38,1,42,NULL,NULL),(39,1,1,NULL,NULL),(40,1,2,NULL,NULL),(41,1,3,NULL,NULL),(42,1,4,NULL,NULL);
/*!40000 ALTER TABLE `company_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_profiles`
--

DROP TABLE IF EXISTS `company_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_bw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp_address` text COLLATE utf8mb4_unicode_ci,
  `invoice_footer` text COLLATE utf8mb4_unicode_ci,
  `letter_footer` text COLLATE utf8mb4_unicode_ci,
  `signature_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_profiles`
--

LOCK TABLES `company_profiles` WRITE;
/*!40000 ALTER TABLE `company_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cost_categories`
--

DROP TABLE IF EXISTS `cost_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cost_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cost_categories_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cost_categories`
--

LOCK TABLES `cost_categories` WRITE;
/*!40000 ALTER TABLE `cost_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `cost_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `costs`
--

DROP TABLE IF EXISTS `costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `costs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_departure_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `cost_category_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('fixed','variable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `costs_branch_id_foreign` (`branch_id`),
  KEY `costs_cost_category_id_foreign` (`cost_category_id`),
  KEY `costs_created_by_foreign` (`created_by`),
  KEY `costs_paket_departure_id_type_index` (`paket_departure_id`,`type`),
  CONSTRAINT `costs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `costs_cost_category_id_foreign` FOREIGN KEY (`cost_category_id`) REFERENCES `cost_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `costs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `costs_paket_departure_id_foreign` FOREIGN KEY (`paket_departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `costs`
--

LOCK TABLES `costs` WRITE;
/*!40000 ALTER TABLE `costs` DISABLE KEYS */;
/*!40000 ALTER TABLE `costs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departure_flight`
--

DROP TABLE IF EXISTS `departure_flight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departure_flight` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `departure_id` bigint unsigned NOT NULL,
  `flight_id` bigint unsigned NOT NULL,
  `type` enum('departure','return') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departure_flight_departure_id_flight_id_type_unique` (`departure_id`,`flight_id`,`type`),
  KEY `departure_flight_flight_id_foreign` (`flight_id`),
  CONSTRAINT `departure_flight_departure_id_foreign` FOREIGN KEY (`departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departure_flight_flight_id_foreign` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departure_flight`
--

LOCK TABLES `departure_flight` WRITE;
/*!40000 ALTER TABLE `departure_flight` DISABLE KEYS */;
/*!40000 ALTER TABLE `departure_flight` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `destinations`
--

DROP TABLE IF EXISTS `destinations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `destinations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('umrah','tour','transit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tour',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `destinations_country_city_index` (`country`,`city`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destinations`
--

LOCK TABLES `destinations` WRITE;
/*!40000 ALTER TABLE `destinations` DISABLE KEYS */;
INSERT INTO `destinations` VALUES (1,'Saudi Arabia','Jakarta','tour',1,'2026-03-27 16:49:06','2026-03-27 16:49:06'),(2,'Saudi Arabia','Jeddah','tour',1,'2026-03-27 16:49:06','2026-03-27 16:49:06');
/*!40000 ALTER TABLE `destinations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flash_sales`
--

DROP TABLE IF EXISTS `flash_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flash_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_id` bigint unsigned NOT NULL,
  `discount_type` enum('fixed','percent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `start_at` timestamp NOT NULL,
  `end_at` timestamp NOT NULL,
  `seat_limit` int DEFAULT NULL,
  `used_seat` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flash_sales_paket_id_foreign` (`paket_id`),
  CONSTRAINT `flash_sales_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_sales`
--

LOCK TABLES `flash_sales` WRITE;
/*!40000 ALTER TABLE `flash_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `flash_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flight_manifests`
--

DROP TABLE IF EXISTS `flight_manifests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_manifests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `flight_id` bigint unsigned NOT NULL,
  `departure_id` bigint unsigned NOT NULL,
  `generated_at` timestamp NOT NULL,
  `generated_by` bigint unsigned DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flight_manifests_flight_id_foreign` (`flight_id`),
  KEY `flight_manifests_departure_id_foreign` (`departure_id`),
  KEY `flight_manifests_generated_by_foreign` (`generated_by`),
  CONSTRAINT `flight_manifests_departure_id_foreign` FOREIGN KEY (`departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flight_manifests_flight_id_foreign` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flight_manifests_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight_manifests`
--

LOCK TABLES `flight_manifests` WRITE;
/*!40000 ALTER TABLE `flight_manifests` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight_manifests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flight_segments`
--

DROP TABLE IF EXISTS `flight_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_segments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `flight_id` bigint unsigned NOT NULL,
  `segment_order` int NOT NULL,
  `origin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `terminal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flight_segments_flight_id_segment_order_unique` (`flight_id`,`segment_order`),
  CONSTRAINT `flight_segments_flight_id_foreign` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight_segments`
--

LOCK TABLES `flight_segments` WRITE;
/*!40000 ALTER TABLE `flight_segments` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight_segments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flights`
--

DROP TABLE IF EXISTS `flights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flights` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `airline` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flight_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aircraft_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aircraft_capacity` int DEFAULT NULL,
  `is_charter` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flights_flight_number_index` (`flight_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flights`
--

LOCK TABLES `flights` WRITE;
/*!40000 ALTER TABLE `flights` DISABLE KEYS */;
/*!40000 ALTER TABLE `flights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jamaah_documents`
--

DROP TABLE IF EXISTS `jamaah_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jamaah_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jamaah_id` bigint unsigned NOT NULL,
  `document_type` enum('passport','visa','ktp','kk','vaccine','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jamaah_documents_jamaah_id_document_type_index` (`jamaah_id`,`document_type`),
  CONSTRAINT `jamaah_documents_jamaah_id_foreign` FOREIGN KEY (`jamaah_id`) REFERENCES `jamaahs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jamaah_documents`
--

LOCK TABLES `jamaah_documents` WRITE;
/*!40000 ALTER TABLE `jamaah_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `jamaah_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jamaahs`
--

DROP TABLE IF EXISTS `jamaahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jamaahs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jamaah_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `agent_id` bigint unsigned DEFAULT NULL,
  `family_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` enum('offline','branch','agent','website') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offline',
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seat_number` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jamaahs_jamaah_code_unique` (`jamaah_code`),
  UNIQUE KEY `jamaahs_nik_unique` (`nik`),
  UNIQUE KEY `jamaahs_passport_number_unique` (`passport_number`),
  KEY `jamaahs_user_id_foreign` (`user_id`),
  KEY `jamaahs_branch_id_foreign` (`branch_id`),
  KEY `jamaahs_family_id_index` (`family_id`),
  KEY `jamaahs_agent_id_foreign` (`agent_id`),
  CONSTRAINT `jamaahs_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `jamaahs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `jamaahs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jamaahs`
--

LOCK TABLES `jamaahs` WRITE;
/*!40000 ALTER TABLE `jamaahs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jamaahs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marketing_addons`
--

DROP TABLE IF EXISTS `marketing_addons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_addons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `selling_price` decimal(15,2) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketing_addons_code_unique` (`code`),
  KEY `marketing_addons_created_by_foreign` (`created_by`),
  CONSTRAINT `marketing_addons_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marketing_addons`
--

LOCK TABLES `marketing_addons` WRITE;
/*!40000 ALTER TABLE `marketing_addons` DISABLE KEYS */;
/*!40000 ALTER TABLE `marketing_addons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marketing_banners`
--

DROP TABLE IF EXISTS `marketing_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_banners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_type` enum('internal','external') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `page` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hero',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `campaign_id` bigint unsigned DEFAULT NULL,
  `target_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_branch_id` bigint unsigned DEFAULT NULL,
  `impressions` bigint unsigned NOT NULL DEFAULT '0',
  `clicks` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketing_banners_campaign_id_foreign` (`campaign_id`),
  KEY `marketing_banners_target_branch_id_foreign` (`target_branch_id`),
  KEY `marketing_banners_page_position_index` (`page`,`position`),
  KEY `marketing_banners_status_index` (`status`),
  CONSTRAINT `marketing_banners_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `marketing_campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `marketing_banners_target_branch_id_foreign` FOREIGN KEY (`target_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marketing_banners`
--

LOCK TABLES `marketing_banners` WRITE;
/*!40000 ALTER TABLE `marketing_banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `marketing_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marketing_campaigns`
--

DROP TABLE IF EXISTS `marketing_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `target_revenue` decimal(15,2) NOT NULL DEFAULT '0.00',
  `budget_marketing` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','active','finished','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketing_campaigns_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `marketing_campaigns_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marketing_campaigns`
--

LOCK TABLES `marketing_campaigns` WRITE;
/*!40000 ALTER TABLE `marketing_campaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `marketing_campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menus_parent_id_foreign` (`parent_id`),
  CONSTRAINT `menus_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'Commission',NULL,'currency-dollar',NULL,'MANAGEMENT',NULL,1,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(2,'Jamaah',NULL,'users',NULL,'OPERATIONS',NULL,2,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(3,'Finance',NULL,'banknotes',NULL,'FINANCE',NULL,3,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(4,'Visa',NULL,'identification',NULL,'VISA',NULL,4,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(5,'Users','users.index','user','user.view','MANAGEMENT',NULL,1,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(6,'Roles','roles.index','shield-check','role.view','MANAGEMENT',NULL,2,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(7,'Branches','branches.index','building-office','branch.view','MANAGEMENT',NULL,3,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(8,'Agents','agents.index','user-group','agent.view','MANAGEMENT',NULL,4,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(9,'Schemes','commission.schemes.index','chart-bar','scheme.view','MANAGEMENT',1,1,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(10,'Config','commission.config.index','cog-6-tooth','config.view','MANAGEMENT',1,2,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(11,'Master Paket','pakets.index','briefcase','paket.view','OPERATIONS',NULL,1,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(12,'Keberangkatan','departures.index','paper-airplane','departure.view','OPERATIONS',NULL,2,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(13,'Bookings','bookings.index','clipboard-document-list','booking.view','OPERATIONS',NULL,3,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(14,'Manifest','manifests.index','document-text','manifest.view','OPERATIONS',NULL,4,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(15,'Rooming List','rooming.index','home-modern','rooming.view','OPERATIONS',NULL,5,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(16,'Data Jamaah','jamaah.index','identification','jamaah.view','OPERATIONS',2,1,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(17,'Dokumen','jamaah.documents.index','document-text','jamaah.document.view','OPERATIONS',2,2,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(18,'Riwayat Booking','jamaah.bookings.history','clock','jamaah.booking.view','OPERATIONS',2,3,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(19,'Approval','jamaah.approvals.index','check-badge','jamaah.approval.view','OPERATIONS',2,4,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(20,'Akun Jamaah','jamaah.account.index','user-circle','jamaah.account.view','OPERATIONS',2,5,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(21,'Dashboard Finance','finance.dashboard','chart-pie','finance.dashboard.view','FINANCE',3,1,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(22,'Pembayaran','finance.payments.index','credit-card','payment.view','FINANCE',3,2,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(23,'Refund','finance.refunds.index','arrow-uturn-left','refund.view','FINANCE',3,3,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(24,'Piutang','finance.receivables.index','clock','receivable.view','FINANCE',3,4,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(25,'Komisi','commission.payouts.index','currency-dollar','commission.payout.view','FINANCE',3,5,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(26,'Cost Management','finance.costs.index','banknotes','cost.view','FINANCE',3,6,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(27,'Laporan','finance.reports.index','document-chart-bar','finance.report.view','FINANCE',3,7,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(28,'Promo Banner','marketing.banners.index','photo','banner.view','MARKETING',NULL,1,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(29,'Campaign','marketing.campaigns.index','rocket-launch','campaign.view','MARKETING',NULL,2,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(30,'Produk Add-On','marketing.addons.index','plus-circle','addon.view','MARKETING',NULL,3,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(31,'Komisi Agent','marketing.agent-commissions.index','chart-bar','agent.performance.view','MARKETING',NULL,4,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(32,'Voucher','marketing.vouchers.index','ticket','voucher.view','MARKETING',NULL,5,1,'2026-03-27 16:34:18','2026-03-27 16:34:18'),(33,'Flash Sale','marketing.flash-sales.index','bolt','flashsale.view','MARKETING',NULL,6,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(34,'Dashboard','ticketing.dashboard','chart-bar','flight.view','TICKETING',NULL,1,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(35,'Data Flight','ticketing.flights.index','paper-airplane','flight.view','TICKETING',NULL,2,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(36,'Departures','ticketing.departures.index','calendar-days','departure.view','TICKETING',NULL,3,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(37,'Seat Allocation','ticketing.seat.index','chair','seat.view','TICKETING',NULL,4,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(38,'Manifest Flight','ticketing.manifests.index','document-text','manifest.view','TICKETING',NULL,5,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(39,'Visa Orders','visa.orders.index','clipboard-document-check','visa.order.view','VISA',4,1,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(40,'Visa Products','visa.products.index','identification','visa.product.view','VISA',4,2,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(41,'Visa Payments','visa.payments.index','credit-card','visa.payment.view','VISA',4,3,1,'2026-03-27 16:34:19','2026-03-27 16:34:19'),(42,'Visa Documents','visa.documents.index','document-check','visa.document.view','VISA',4,4,1,'2026-03-27 16:34:19','2026-03-27 16:34:19');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_02_11_000000_create_pakets_table',1),(5,'2026_02_11_075639_create_permission_tables',1),(6,'2026_02_11_080947_create_branches_table',1),(7,'2026_02_11_081218_add_branch_id_to_users_table',1),(8,'2026_02_11_140531_create_company_profiles_table',1),(9,'2026_02_11_140603_create_company_bank_accounts_table',1),(10,'2026_02_11_231441_create_agents_table',1),(11,'2026_02_12_020801_drop_static_commission_columns_from_agents',1),(12,'2026_02_12_020900_create_jamaahs_table',1),(13,'2026_02_12_021043_create_commission_schemes_table',1),(14,'2026_02_12_021125_create_commission_company_rules_table',1),(15,'2026_02_12_021200_create_paket_departures_table',1),(16,'2026_02_12_021201_create_commission_branch_rules_table',1),(17,'2026_02_12_021232_create_agent_tiers_table',1),(18,'2026_02_12_021307_create_bookings_table',1),(19,'2026_02_12_021308_create_commission_logs_table',1),(20,'2026_02_13_091852_create_companies_table',1),(21,'2026_02_13_091853_create_menus_table',1),(22,'2026_02_13_091854_create_company_menu_table',1),(23,'2026_02_13_091854_create_role_menu_table',1),(24,'2026_02_14_063115_alter_pakets_table_upgrade',1),(25,'2026_02_14_063309_create_paket_prices_table',1),(26,'2026_02_14_063352_create_paket_hotels_table',1),(27,'2026_02_14_063738_create_destinations_table',1),(28,'2026_02_14_063812_create_paket_destinations_table',1),(29,'2026_02_14_131135_create_jamaah_documents_table',1),(30,'2026_02_14_131627_create_booking_jamaah_table',1),(31,'2026_02_14_131836_alter_jamaahs_structure',1),(32,'2026_02_14_145527_add_agent_id_to_jamaahs_table',1),(33,'2026_02_15_131712_alter_paket_departures_drop_price_override',1),(34,'2026_02_15_132023_create_paket_departure_prices_table',1),(35,'2026_02_16_115211_alter_commission_logs_nullable',1),(36,'2026_02_16_121417_add_fk_to_commission_logs_jamaah',1),(37,'2026_02_16_134531_alter_paket_departures_add_operational_fields',1),(38,'2026_02_17_000225_add_seat_number_to_booking_jamaah',1),(39,'2026_02_17_001833_add_seat_number_to_jamaahs_table',1),(40,'2026_02_17_064531_create_rooms_table',1),(41,'2026_02_17_064532_create_room_members_table',1),(42,'2026_02_17_080143_add_family_id_to_jamaahs_table',1),(43,'2026_02_17_120857_create_payments_table',1),(44,'2026_02_17_133144_rename_departure_id_on_payments_table',1),(45,'2026_02_17_134033_make_branch_nullable_on_payments',1),(46,'2026_02_17_135304_add_approval_to_payments_table',1),(47,'2026_02_17_141317_alter_type_enum_on_payments_table',1),(48,'2026_02_18_002216_create_code_counters_table',1),(49,'2026_02_18_110857_add_proof_to_payments_table',1),(50,'2026_02_18_135256_add_receipt_number_to_payments_table',1),(51,'2026_02_18_140724_add_invoice_number_to_payments_table',1),(52,'2026_02_18_210403_create_refunds_table',1),(53,'2026_02_18_220932_add_receipt_number_to_refunds_table',1),(54,'2026_02_19_132615_create_commission_payouts_table',1),(55,'2026_02_19_132649_create_commission_payout_items_table',1),(56,'2026_02_19_140043_create_cost_categories_table',1),(57,'2026_02_19_140044_create_costs_table',1),(58,'2026_02_20_081754_add_is_active_to_cost_categories_table',1),(59,'2026_02_20_141658_create_booking_addons_table',1),(60,'2026_02_20_141658_create_marketing_addons_table',1),(61,'2026_02_21_121212_create_marketing_campaigns_table',1),(62,'2026_02_21_121316_create_campaign_paket_table',1),(63,'2026_02_21_121345_add_campaign_id_to_bookings_table',1),(64,'2026_02_21_132611_create_marketing_banners_table',1),(65,'2026_02_21_142051_create_vouchers_table',1),(66,'2026_02_21_142117_add_voucher_to_bookings',1),(67,'2026_02_21_144039_create_flash_sales_table',1),(68,'2026_02_21_222110_create_flights_table',1),(69,'2026_02_21_222118_create_departure_flight_table',1),(70,'2026_02_21_222118_create_flight_manifests_table',1),(71,'2026_02_21_222118_create_flight_segments_table',1),(72,'2026_02_21_222118_create_seat_allocations_table',1),(73,'2026_02_25_144732_add_codes_to_bookings_table',1),(74,'2026_02_25_212910_create_personal_access_tokens_table',1),(75,'2026_02_27_151804_add_snapshot_fields_to_bookings_table',1),(76,'2026_03_02_143159_add_expire_index_to_bookings',1),(77,'2026_03_02_145652_alter_status_enum_on_bookings_table',1),(78,'2026_03_02_194344_update_booking_status_enum',1),(79,'2026_03_03_123648_add_user_ownership_to_bookings',1),(80,'2026_03_03_131855_migrate_jamaah_agent_identity_to_users',1),(81,'2026_03_04_210101_fix_booking_agent_fk_to_users',1),(82,'2026_03_04_220627_fix_commission_logs_agent_fk',1),(83,'2026_03_05_202419_add_partial_paid_to_booking_status',1),(84,'2026_03_10_182238_drop_price_from_pakets_table',1),(85,'2026_03_11_084005_create_saving_accounts_table',1),(86,'2026_03_11_084106_create_saving_transactions_table',1),(87,'2026_03_11_084155_create_saving_goals_table',1),(88,'2026_03_12_205257_alter_payments_add_finance_fields',1),(89,'2026_03_12_205553_create_payment_logs_table',1),(90,'2026_03_12_222924_drop_unique_invoice_payments',1),(91,'2026_03_15_132645_create_visa_products_table',1),(92,'2026_03_15_132745_create_visa_orders_table',1),(93,'2026_03_15_132803_create_visa_order_travelers_table',1),(94,'2026_03_15_132820_create_visa_order_documents_table',1),(95,'2026_03_15_132832_create_visa_payments_table',1),(96,'2026_03_15_132846_create_visa_status_histories_table',1),(97,'2026_03_15_132858_create_visa_order_notes_table',1),(98,'2026_03_16_180043_normalize_jamaah_identity_fields',1),(99,'2026_03_16_180501_add_unique_indexes_to_jamaahs_identity_fields',1),(100,'2026_03_17_132307_add_promo_fields_to_pakets',1),(101,'2026_03_17_204207_add_promo_fields_to_paket_departure_prices_table',1),(102,'2026_03_18_181955_create_booking_locks_table',1),(103,'2026_03_18_223305_add_snapshot_columns_to_bookings',1),(104,'2026_03_19_133120_add_discount_snapshot_to_bookings_table',1),(105,'2026_03_26_202949_add_proper_indexes_to_paket_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(4,'App\\Models\\User',3);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_departure_prices`
--

DROP TABLE IF EXISTS `paket_departure_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket_departure_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_departure_id` bigint unsigned NOT NULL,
  `room_type` enum('double','triple','quad') COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `promo_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_value` int DEFAULT NULL,
  `promo_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paket_departure_prices_paket_departure_id_room_type_unique` (`paket_departure_id`,`room_type`),
  KEY `idx_paket_departure_prices_main` (`paket_departure_id`,`price`),
  KEY `idx_price_departure` (`paket_departure_id`),
  CONSTRAINT `paket_departure_prices_paket_departure_id_foreign` FOREIGN KEY (`paket_departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_departure_prices`
--

LOCK TABLES `paket_departure_prices` WRITE;
/*!40000 ALTER TABLE `paket_departure_prices` DISABLE KEYS */;
INSERT INTO `paket_departure_prices` VALUES (1,1,'double',40000000.00,'fixed',1000000,'Hot Sale',NULL,'2026-03-27 16:49:06','2026-03-27 16:49:06'),(2,1,'triple',37000000.00,'fixed',1000000,'Hot Sale',NULL,'2026-03-27 16:49:06','2026-03-27 16:49:06'),(3,1,'quad',35000000.00,'fixed',1000000,'Hot Sale',NULL,'2026-03-27 16:49:06','2026-03-27 16:49:06');
/*!40000 ALTER TABLE `paket_departure_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_departures`
--

DROP TABLE IF EXISTS `paket_departures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket_departures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_id` bigint unsigned NOT NULL,
  `departure_code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flight_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_point` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `quota` int NOT NULL,
  `booked` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paket_departures_paket_id_departure_date_index` (`paket_id`,`departure_date`),
  KEY `idx_paket_departures_main` (`paket_id`,`is_active`,`is_closed`,`departure_date`),
  KEY `idx_departure_paket` (`paket_id`),
  KEY `idx_departure_date` (`departure_date`),
  CONSTRAINT `paket_departures_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_departures`
--

LOCK TABLES `paket_departures` WRITE;
/*!40000 ALTER TABLE `paket_departures` DISABLE KEYS */;
INSERT INTO `paket_departures` VALUES (1,1,NULL,NULL,NULL,'2026-06-27','2026-07-08',45,0,1,0,'2026-03-27 16:49:06','2026-03-27 16:49:06');
/*!40000 ALTER TABLE `paket_departures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_destinations`
--

DROP TABLE IF EXISTS `paket_destinations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket_destinations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_id` bigint unsigned NOT NULL,
  `destination_id` bigint unsigned NOT NULL,
  `day_order` int unsigned NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paket_destinations_paket_id_day_order_unique` (`paket_id`,`day_order`),
  KEY `paket_destinations_destination_id_foreign` (`destination_id`),
  CONSTRAINT `paket_destinations_destination_id_foreign` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paket_destinations_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_destinations`
--

LOCK TABLES `paket_destinations` WRITE;
/*!40000 ALTER TABLE `paket_destinations` DISABLE KEYS */;
INSERT INTO `paket_destinations` VALUES (1,1,1,1,'Hari Pertama Keberangkata','2026-03-27 16:49:06','2026-03-27 16:49:06'),(2,1,2,2,'Hari Kedua Tiba di Jeddah Saudi Arabai','2026-03-27 16:49:06','2026-03-27 16:49:06');
/*!40000 ALTER TABLE `paket_destinations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_hotels`
--

DROP TABLE IF EXISTS `paket_hotels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket_hotels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_id` bigint unsigned NOT NULL,
  `city` enum('mekkah','madinah') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int DEFAULT NULL,
  `distance_to_haram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paket_hotels_paket_id_foreign` (`paket_id`),
  CONSTRAINT `paket_hotels_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_hotels`
--

LOCK TABLES `paket_hotels` WRITE;
/*!40000 ALTER TABLE `paket_hotels` DISABLE KEYS */;
INSERT INTO `paket_hotels` VALUES (1,1,'mekkah','Mekkah Tower',5,'100 Meter','2026-03-27 16:49:06','2026-03-27 16:49:06'),(2,1,'madinah','Hilton',5,'100 Meter','2026-03-27 16:49:06','2026-03-27 16:49:06');
/*!40000 ALTER TABLE `paket_hotels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_prices`
--

DROP TABLE IF EXISTS `paket_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paket_id` bigint unsigned NOT NULL,
  `room_type` enum('double','triple','quad') COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paket_prices_paket_id_foreign` (`paket_id`),
  CONSTRAINT `paket_prices_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `pakets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_prices`
--

LOCK TABLES `paket_prices` WRITE;
/*!40000 ALTER TABLE `paket_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `paket_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pakets`
--

DROP TABLE IF EXISTS `pakets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pakets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `duration_days` int DEFAULT NULL,
  `airline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quota` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery` json DEFAULT NULL,
  `promo_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_value` int DEFAULT NULL,
  `promo_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pakets_code_unique` (`code`),
  KEY `idx_pakets_slug` (`slug`),
  KEY `idx_pakets_status` (`is_active`,`is_published`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pakets`
--

LOCK TABLES `pakets` WRITE;
/*!40000 ALTER TABLE `pakets` DISABLE KEYS */;
INSERT INTO `pakets` VALUES (1,'Umrah Plus Thaif','UMR-001','umrah-plus-thaif-p7plS','Jakarta',NULL,NULL,11,'Saudi Airways',NULL,1,1,'2026-03-27 16:49:06','2026-03-27 16:49:06','Umrah Plus Thaif','Harga Sudah Termasuk \r\n1. Perlengkapan \r\n2.','pakets/NV5T5TVCWVujr9yDf0FLZhFOncOD4I2DA7TViyFj.png','[\"pakets/Yp8OS4bRcUlA6af3EipvdpNZfHjVkkLdNDoAMgdI.png\"]',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `pakets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_logs`
--

DROP TABLE IF EXISTS `payment_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_data` json DEFAULT NULL,
  `new_data` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_logs_payment_id_foreign` (`payment_id`),
  KEY `payment_logs_created_by_foreign` (`created_by`),
  CONSTRAINT `payment_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_logs_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_logs`
--

LOCK TABLES `payment_logs` WRITE;
/*!40000 ALTER TABLE `payment_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `jamaah_id` bigint unsigned DEFAULT NULL,
  `paket_departure_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `payment_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('dp','cicilan','pelunasan','add_on','upgrade','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` enum('transfer','cash','gateway','edc') COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` enum('website','agent','admin','gateway') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  `amount` decimal(15,2) NOT NULL,
  `fee_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','paid','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `paid_at` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `proof_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_code_unique` (`payment_code`),
  UNIQUE KEY `payments_receipt_number_unique` (`receipt_number`),
  KEY `payments_booking_id_foreign` (`booking_id`),
  KEY `payments_jamaah_id_foreign` (`jamaah_id`),
  KEY `payments_created_by_foreign` (`created_by`),
  KEY `payments_departure_id_index` (`paket_departure_id`),
  KEY `payments_branch_id_index` (`branch_id`),
  KEY `payments_status_index` (`status`),
  KEY `payments_type_index` (`type`),
  KEY `payments_approved_by_foreign` (`approved_by`),
  CONSTRAINT `payments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_departure_id_foreign` FOREIGN KEY (`paket_departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_jamaah_id_foreign` FOREIGN KEY (`jamaah_id`) REFERENCES `jamaahs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(2,'user.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(3,'user.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(4,'user.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(5,'user.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(6,'role.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(7,'role.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(8,'role.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(9,'role.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(10,'branch.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(11,'branch.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(12,'branch.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(13,'branch.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(14,'agent.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(15,'agent.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(16,'agent.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(17,'agent.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(18,'jamaah.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(19,'jamaah.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(20,'jamaah.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(21,'jamaah.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(22,'jamaah.document.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(23,'jamaah.approval.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(24,'jamaah.booking.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(25,'jamaah.approve','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(26,'jamaah.account.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(27,'jamaah.account.reset','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(28,'jamaah.document.upload','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(29,'jamaah.document.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(30,'paket.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(31,'paket.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(32,'paket.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(33,'paket.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(34,'manifest.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(35,'manifest.export','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(36,'rooming.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(37,'rooming.generate','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(38,'rooming.assign','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(39,'rooming.export','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(40,'departure.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(41,'departure.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(42,'departure.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(43,'departure.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(44,'booking.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(45,'booking.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(46,'booking.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(47,'booking.approve','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(48,'booking.cancel','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(49,'scheme.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(50,'scheme.create','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(51,'scheme.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(52,'scheme.delete','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(53,'config.view','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(54,'config.update','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(55,'finance.dashboard.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(56,'payment.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(57,'payment.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(58,'payment.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(59,'payment.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(60,'payment.approve','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(61,'payment.receipt.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(62,'refund.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(63,'refund.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(64,'refund.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(65,'refund.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(66,'refund.approve','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(67,'receivable.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(68,'commission.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(69,'commission.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(70,'commission.payout.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(71,'commission.payout.request','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(72,'commission.payout.approve','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(73,'commission.payout.pay','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(74,'finance.report.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(75,'finance.report.export','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(76,'cost.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(77,'cost.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(78,'cost.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(79,'cost.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(80,'cost.approve','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(81,'campaign.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(82,'campaign.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(83,'campaign.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(84,'campaign.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(85,'addon.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(86,'addon.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(87,'addon.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(88,'addon.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(89,'banner.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(90,'banner.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(91,'banner.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(92,'banner.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(93,'voucher.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(94,'voucher.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(95,'voucher.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(96,'voucher.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(97,'flashsale.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(98,'flashsale.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(99,'flashsale.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(100,'flashsale.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(101,'flight.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(102,'flight.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(103,'flight.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(104,'flight.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(105,'seat.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(106,'seat.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(107,'manifest.generate','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(108,'visa.product.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(109,'visa.product.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(110,'visa.product.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(111,'visa.product.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(112,'visa.order.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(113,'visa.order.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(114,'visa.order.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(115,'visa.order.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(116,'visa.order.status.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(117,'visa.order.note.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(118,'visa.order.traveler.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(119,'visa.order.traveler.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(120,'visa.order.traveler.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(121,'visa.payment.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(122,'visa.payment.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(123,'visa.payment.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(124,'visa.payment.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(125,'visa.payment.approve','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(126,'visa.payment.refund','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(127,'visa.document.view','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(128,'visa.document.create','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(129,'visa.document.update','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(130,'visa.document.delete','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(131,'visa.document.verify','web','2026-03-27 16:34:18','2026-03-27 16:34:18'),(132,'visa.document.download','web','2026-03-27 16:34:18','2026-03-27 16:34:18');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `refunds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `refund_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refund_receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refunds_refund_code_unique` (`refund_code`),
  UNIQUE KEY `refunds_refund_receipt_number_unique` (`refund_receipt_number`),
  KEY `refunds_payment_id_foreign` (`payment_id`),
  KEY `refunds_booking_id_foreign` (`booking_id`),
  KEY `refunds_branch_id_foreign` (`branch_id`),
  KEY `refunds_created_by_foreign` (`created_by`),
  KEY `refunds_approved_by_foreign` (`approved_by`),
  CONSTRAINT `refunds_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `refunds_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `refunds_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `refunds_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1),(78,1),(79,1),(80,1),(81,1),(82,1),(83,1),(84,1),(85,1),(86,1),(87,1),(88,1),(89,1),(90,1),(91,1),(92,1),(93,1),(94,1),(95,1),(96,1),(97,1),(98,1),(99,1),(100,1),(101,1),(102,1),(103,1),(104,1),(105,1),(106,1),(107,1),(108,1),(109,1),(110,1),(111,1),(112,1),(113,1),(114,1),(115,1),(116,1),(117,1),(118,1),(119,1),(120,1),(121,1),(122,1),(123,1),(124,1),(125,1),(126,1),(127,1),(128,1),(129,1),(130,1),(131,1),(132,1),(1,2),(18,2),(30,2),(44,2),(47,2),(49,2),(56,2),(62,2),(108,2),(109,2),(110,2),(111,2),(112,2),(113,2),(114,2),(115,2),(116,2),(117,2),(118,2),(119,2),(120,2),(121,2),(122,2),(123,2),(124,2),(125,2),(126,2),(127,2),(128,2),(129,2),(130,2),(131,2),(132,2),(1,3),(56,3),(59,3),(60,3),(62,3),(63,3),(65,3),(66,3),(68,3),(70,3),(72,3),(73,3),(76,3),(77,3),(78,3),(79,3),(80,3),(112,3),(121,3),(122,3),(123,3),(124,3),(125,3),(126,3),(127,3),(132,3),(1,4),(14,4),(15,4),(16,4),(18,4),(19,4),(20,4),(22,4),(28,4),(29,4),(30,4),(44,4),(45,4),(46,4),(56,4),(57,4),(108,4),(112,4),(113,4),(114,4),(116,4),(117,4),(118,4),(119,4),(120,4),(121,4),(122,4),(123,4),(127,4),(128,4),(129,4),(132,4),(1,5),(68,5),(70,5),(112,5),(121,5),(127,5),(132,5),(18,6),(44,6),(112,6),(127,6),(128,6),(132,6),(1,8),(18,8),(19,8),(20,8),(22,8),(28,8),(29,8),(44,8),(45,8),(71,8),(1,9),(22,9),(28,9),(44,9),(45,9),(56,9),(57,9),(58,9);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_menu`
--

DROP TABLE IF EXISTS `role_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `menu_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_menu_role_id_foreign` (`role_id`),
  KEY `role_menu_menu_id_foreign` (`menu_id`),
  CONSTRAINT `role_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_menu_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=379 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_menu`
--

LOCK TABLES `role_menu` WRITE;
/*!40000 ALTER TABLE `role_menu` DISABLE KEYS */;
INSERT INTO `role_menu` VALUES (1,1,5,NULL,NULL),(2,2,5,NULL,NULL),(3,3,5,NULL,NULL),(4,4,5,NULL,NULL),(5,5,5,NULL,NULL),(6,6,5,NULL,NULL),(7,7,5,NULL,NULL),(8,8,5,NULL,NULL),(9,9,5,NULL,NULL),(10,1,6,NULL,NULL),(11,2,6,NULL,NULL),(12,3,6,NULL,NULL),(13,4,6,NULL,NULL),(14,5,6,NULL,NULL),(15,6,6,NULL,NULL),(16,7,6,NULL,NULL),(17,8,6,NULL,NULL),(18,9,6,NULL,NULL),(19,1,7,NULL,NULL),(20,2,7,NULL,NULL),(21,3,7,NULL,NULL),(22,4,7,NULL,NULL),(23,5,7,NULL,NULL),(24,6,7,NULL,NULL),(25,7,7,NULL,NULL),(26,8,7,NULL,NULL),(27,9,7,NULL,NULL),(28,1,8,NULL,NULL),(29,2,8,NULL,NULL),(30,3,8,NULL,NULL),(31,4,8,NULL,NULL),(32,5,8,NULL,NULL),(33,6,8,NULL,NULL),(34,7,8,NULL,NULL),(35,8,8,NULL,NULL),(36,9,8,NULL,NULL),(37,1,9,NULL,NULL),(38,2,9,NULL,NULL),(39,3,9,NULL,NULL),(40,4,9,NULL,NULL),(41,5,9,NULL,NULL),(42,6,9,NULL,NULL),(43,7,9,NULL,NULL),(44,8,9,NULL,NULL),(45,9,9,NULL,NULL),(46,1,10,NULL,NULL),(47,2,10,NULL,NULL),(48,3,10,NULL,NULL),(49,4,10,NULL,NULL),(50,5,10,NULL,NULL),(51,6,10,NULL,NULL),(52,7,10,NULL,NULL),(53,8,10,NULL,NULL),(54,9,10,NULL,NULL),(55,1,11,NULL,NULL),(56,2,11,NULL,NULL),(57,3,11,NULL,NULL),(58,4,11,NULL,NULL),(59,5,11,NULL,NULL),(60,6,11,NULL,NULL),(61,7,11,NULL,NULL),(62,8,11,NULL,NULL),(63,9,11,NULL,NULL),(64,1,12,NULL,NULL),(65,2,12,NULL,NULL),(66,3,12,NULL,NULL),(67,4,12,NULL,NULL),(68,5,12,NULL,NULL),(69,6,12,NULL,NULL),(70,7,12,NULL,NULL),(71,8,12,NULL,NULL),(72,9,12,NULL,NULL),(73,1,13,NULL,NULL),(74,2,13,NULL,NULL),(75,3,13,NULL,NULL),(76,4,13,NULL,NULL),(77,5,13,NULL,NULL),(78,6,13,NULL,NULL),(79,7,13,NULL,NULL),(80,8,13,NULL,NULL),(81,9,13,NULL,NULL),(82,1,14,NULL,NULL),(83,2,14,NULL,NULL),(84,3,14,NULL,NULL),(85,4,14,NULL,NULL),(86,5,14,NULL,NULL),(87,6,14,NULL,NULL),(88,7,14,NULL,NULL),(89,8,14,NULL,NULL),(90,9,14,NULL,NULL),(91,1,15,NULL,NULL),(92,2,15,NULL,NULL),(93,3,15,NULL,NULL),(94,4,15,NULL,NULL),(95,5,15,NULL,NULL),(96,6,15,NULL,NULL),(97,7,15,NULL,NULL),(98,8,15,NULL,NULL),(99,9,15,NULL,NULL),(100,1,16,NULL,NULL),(101,2,16,NULL,NULL),(102,3,16,NULL,NULL),(103,4,16,NULL,NULL),(104,5,16,NULL,NULL),(105,6,16,NULL,NULL),(106,7,16,NULL,NULL),(107,8,16,NULL,NULL),(108,9,16,NULL,NULL),(109,1,17,NULL,NULL),(110,2,17,NULL,NULL),(111,3,17,NULL,NULL),(112,4,17,NULL,NULL),(113,5,17,NULL,NULL),(114,6,17,NULL,NULL),(115,7,17,NULL,NULL),(116,8,17,NULL,NULL),(117,9,17,NULL,NULL),(118,1,18,NULL,NULL),(119,2,18,NULL,NULL),(120,3,18,NULL,NULL),(121,4,18,NULL,NULL),(122,5,18,NULL,NULL),(123,6,18,NULL,NULL),(124,7,18,NULL,NULL),(125,8,18,NULL,NULL),(126,9,18,NULL,NULL),(127,1,19,NULL,NULL),(128,2,19,NULL,NULL),(129,3,19,NULL,NULL),(130,4,19,NULL,NULL),(131,5,19,NULL,NULL),(132,6,19,NULL,NULL),(133,7,19,NULL,NULL),(134,8,19,NULL,NULL),(135,9,19,NULL,NULL),(136,1,20,NULL,NULL),(137,2,20,NULL,NULL),(138,3,20,NULL,NULL),(139,4,20,NULL,NULL),(140,5,20,NULL,NULL),(141,6,20,NULL,NULL),(142,7,20,NULL,NULL),(143,8,20,NULL,NULL),(144,9,20,NULL,NULL),(145,1,21,NULL,NULL),(146,2,21,NULL,NULL),(147,3,21,NULL,NULL),(148,4,21,NULL,NULL),(149,5,21,NULL,NULL),(150,6,21,NULL,NULL),(151,7,21,NULL,NULL),(152,8,21,NULL,NULL),(153,9,21,NULL,NULL),(154,1,22,NULL,NULL),(155,2,22,NULL,NULL),(156,3,22,NULL,NULL),(157,4,22,NULL,NULL),(158,5,22,NULL,NULL),(159,6,22,NULL,NULL),(160,7,22,NULL,NULL),(161,8,22,NULL,NULL),(162,9,22,NULL,NULL),(163,1,23,NULL,NULL),(164,2,23,NULL,NULL),(165,3,23,NULL,NULL),(166,4,23,NULL,NULL),(167,5,23,NULL,NULL),(168,6,23,NULL,NULL),(169,7,23,NULL,NULL),(170,8,23,NULL,NULL),(171,9,23,NULL,NULL),(172,1,24,NULL,NULL),(173,2,24,NULL,NULL),(174,3,24,NULL,NULL),(175,4,24,NULL,NULL),(176,5,24,NULL,NULL),(177,6,24,NULL,NULL),(178,7,24,NULL,NULL),(179,8,24,NULL,NULL),(180,9,24,NULL,NULL),(181,1,25,NULL,NULL),(182,2,25,NULL,NULL),(183,3,25,NULL,NULL),(184,4,25,NULL,NULL),(185,5,25,NULL,NULL),(186,6,25,NULL,NULL),(187,7,25,NULL,NULL),(188,8,25,NULL,NULL),(189,9,25,NULL,NULL),(190,1,26,NULL,NULL),(191,2,26,NULL,NULL),(192,3,26,NULL,NULL),(193,4,26,NULL,NULL),(194,5,26,NULL,NULL),(195,6,26,NULL,NULL),(196,7,26,NULL,NULL),(197,8,26,NULL,NULL),(198,9,26,NULL,NULL),(199,1,27,NULL,NULL),(200,2,27,NULL,NULL),(201,3,27,NULL,NULL),(202,4,27,NULL,NULL),(203,5,27,NULL,NULL),(204,6,27,NULL,NULL),(205,7,27,NULL,NULL),(206,8,27,NULL,NULL),(207,9,27,NULL,NULL),(208,1,28,NULL,NULL),(209,2,28,NULL,NULL),(210,3,28,NULL,NULL),(211,4,28,NULL,NULL),(212,5,28,NULL,NULL),(213,6,28,NULL,NULL),(214,7,28,NULL,NULL),(215,8,28,NULL,NULL),(216,9,28,NULL,NULL),(217,1,29,NULL,NULL),(218,2,29,NULL,NULL),(219,3,29,NULL,NULL),(220,4,29,NULL,NULL),(221,5,29,NULL,NULL),(222,6,29,NULL,NULL),(223,7,29,NULL,NULL),(224,8,29,NULL,NULL),(225,9,29,NULL,NULL),(226,1,30,NULL,NULL),(227,2,30,NULL,NULL),(228,3,30,NULL,NULL),(229,4,30,NULL,NULL),(230,5,30,NULL,NULL),(231,6,30,NULL,NULL),(232,7,30,NULL,NULL),(233,8,30,NULL,NULL),(234,9,30,NULL,NULL),(235,1,31,NULL,NULL),(236,2,31,NULL,NULL),(237,3,31,NULL,NULL),(238,4,31,NULL,NULL),(239,5,31,NULL,NULL),(240,6,31,NULL,NULL),(241,7,31,NULL,NULL),(242,8,31,NULL,NULL),(243,9,31,NULL,NULL),(244,1,32,NULL,NULL),(245,2,32,NULL,NULL),(246,3,32,NULL,NULL),(247,4,32,NULL,NULL),(248,5,32,NULL,NULL),(249,6,32,NULL,NULL),(250,7,32,NULL,NULL),(251,8,32,NULL,NULL),(252,9,32,NULL,NULL),(253,1,33,NULL,NULL),(254,2,33,NULL,NULL),(255,3,33,NULL,NULL),(256,4,33,NULL,NULL),(257,5,33,NULL,NULL),(258,6,33,NULL,NULL),(259,7,33,NULL,NULL),(260,8,33,NULL,NULL),(261,9,33,NULL,NULL),(262,1,34,NULL,NULL),(263,2,34,NULL,NULL),(264,3,34,NULL,NULL),(265,4,34,NULL,NULL),(266,5,34,NULL,NULL),(267,6,34,NULL,NULL),(268,7,34,NULL,NULL),(269,8,34,NULL,NULL),(270,9,34,NULL,NULL),(271,1,35,NULL,NULL),(272,2,35,NULL,NULL),(273,3,35,NULL,NULL),(274,4,35,NULL,NULL),(275,5,35,NULL,NULL),(276,6,35,NULL,NULL),(277,7,35,NULL,NULL),(278,8,35,NULL,NULL),(279,9,35,NULL,NULL),(280,1,36,NULL,NULL),(281,2,36,NULL,NULL),(282,3,36,NULL,NULL),(283,4,36,NULL,NULL),(284,5,36,NULL,NULL),(285,6,36,NULL,NULL),(286,7,36,NULL,NULL),(287,8,36,NULL,NULL),(288,9,36,NULL,NULL),(289,1,37,NULL,NULL),(290,2,37,NULL,NULL),(291,3,37,NULL,NULL),(292,4,37,NULL,NULL),(293,5,37,NULL,NULL),(294,6,37,NULL,NULL),(295,7,37,NULL,NULL),(296,8,37,NULL,NULL),(297,9,37,NULL,NULL),(298,1,38,NULL,NULL),(299,2,38,NULL,NULL),(300,3,38,NULL,NULL),(301,4,38,NULL,NULL),(302,5,38,NULL,NULL),(303,6,38,NULL,NULL),(304,7,38,NULL,NULL),(305,8,38,NULL,NULL),(306,9,38,NULL,NULL),(307,1,39,NULL,NULL),(308,2,39,NULL,NULL),(309,3,39,NULL,NULL),(310,4,39,NULL,NULL),(311,5,39,NULL,NULL),(312,6,39,NULL,NULL),(313,7,39,NULL,NULL),(314,8,39,NULL,NULL),(315,9,39,NULL,NULL),(316,1,40,NULL,NULL),(317,2,40,NULL,NULL),(318,3,40,NULL,NULL),(319,4,40,NULL,NULL),(320,5,40,NULL,NULL),(321,6,40,NULL,NULL),(322,7,40,NULL,NULL),(323,8,40,NULL,NULL),(324,9,40,NULL,NULL),(325,1,41,NULL,NULL),(326,2,41,NULL,NULL),(327,3,41,NULL,NULL),(328,4,41,NULL,NULL),(329,5,41,NULL,NULL),(330,6,41,NULL,NULL),(331,7,41,NULL,NULL),(332,8,41,NULL,NULL),(333,9,41,NULL,NULL),(334,1,42,NULL,NULL),(335,2,42,NULL,NULL),(336,3,42,NULL,NULL),(337,4,42,NULL,NULL),(338,5,42,NULL,NULL),(339,6,42,NULL,NULL),(340,7,42,NULL,NULL),(341,8,42,NULL,NULL),(342,9,42,NULL,NULL),(343,1,1,NULL,NULL),(344,2,1,NULL,NULL),(345,3,1,NULL,NULL),(346,4,1,NULL,NULL),(347,5,1,NULL,NULL),(348,6,1,NULL,NULL),(349,7,1,NULL,NULL),(350,8,1,NULL,NULL),(351,9,1,NULL,NULL),(352,1,2,NULL,NULL),(353,2,2,NULL,NULL),(354,3,2,NULL,NULL),(355,4,2,NULL,NULL),(356,5,2,NULL,NULL),(357,6,2,NULL,NULL),(358,7,2,NULL,NULL),(359,8,2,NULL,NULL),(360,9,2,NULL,NULL),(361,1,3,NULL,NULL),(362,2,3,NULL,NULL),(363,3,3,NULL,NULL),(364,4,3,NULL,NULL),(365,5,3,NULL,NULL),(366,6,3,NULL,NULL),(367,7,3,NULL,NULL),(368,8,3,NULL,NULL),(369,9,3,NULL,NULL),(370,1,4,NULL,NULL),(371,2,4,NULL,NULL),(372,3,4,NULL,NULL),(373,4,4,NULL,NULL),(374,5,4,NULL,NULL),(375,6,4,NULL,NULL),(376,7,4,NULL,NULL),(377,8,4,NULL,NULL),(378,9,4,NULL,NULL);
/*!40000 ALTER TABLE `role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'SUPERADMIN','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(2,'ADMIN_PUSAT','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(3,'FINANCE','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(4,'ADMIN_CABANG','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(5,'KEUANGAN_CABANG','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(6,'OPERATOR_CABANG','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(7,'CRM_CABANG','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(8,'AGENT','web','2026-03-27 16:34:17','2026-03-27 16:34:17'),(9,'JAMAAH','web','2026-03-27 16:34:17','2026-03-27 16:34:17');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_members`
--

DROP TABLE IF EXISTS `room_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_id` bigint unsigned NOT NULL,
  `jamaah_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_members_room_id_jamaah_id_unique` (`room_id`,`jamaah_id`),
  KEY `room_members_jamaah_id_foreign` (`jamaah_id`),
  CONSTRAINT `room_members_jamaah_id_foreign` FOREIGN KEY (`jamaah_id`) REFERENCES `jamaahs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_members_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_members`
--

LOCK TABLES `room_members` WRITE;
/*!40000 ALTER TABLE `room_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `room_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `departure_id` bigint unsigned NOT NULL,
  `hotel_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int NOT NULL DEFAULT '4',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rooms_departure_id_foreign` (`departure_id`),
  CONSTRAINT `rooms_departure_id_foreign` FOREIGN KEY (`departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saving_accounts`
--

DROP TABLE IF EXISTS `saving_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saving_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `jamaah_id` bigint unsigned DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','active','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `saving_accounts_account_number_unique` (`account_number`),
  KEY `saving_accounts_user_id_foreign` (`user_id`),
  KEY `saving_accounts_jamaah_id_foreign` (`jamaah_id`),
  CONSTRAINT `saving_accounts_jamaah_id_foreign` FOREIGN KEY (`jamaah_id`) REFERENCES `jamaahs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `saving_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saving_accounts`
--

LOCK TABLES `saving_accounts` WRITE;
/*!40000 ALTER TABLE `saving_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `saving_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saving_goals`
--

DROP TABLE IF EXISTS `saving_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saving_goals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `saving_account_id` bigint unsigned NOT NULL,
  `goal_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_amount` decimal(15,2) NOT NULL,
  `target_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `saving_goals_saving_account_id_foreign` (`saving_account_id`),
  CONSTRAINT `saving_goals_saving_account_id_foreign` FOREIGN KEY (`saving_account_id`) REFERENCES `saving_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saving_goals`
--

LOCK TABLES `saving_goals` WRITE;
/*!40000 ALTER TABLE `saving_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `saving_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saving_transactions`
--

DROP TABLE IF EXISTS `saving_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saving_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `saving_account_id` bigint unsigned NOT NULL,
  `type` enum('deposit','withdraw','convert_booking') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `saving_transactions_saving_account_id_foreign` (`saving_account_id`),
  CONSTRAINT `saving_transactions_saving_account_id_foreign` FOREIGN KEY (`saving_account_id`) REFERENCES `saving_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saving_transactions`
--

LOCK TABLES `saving_transactions` WRITE;
/*!40000 ALTER TABLE `saving_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `saving_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seat_allocations`
--

DROP TABLE IF EXISTS `seat_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seat_allocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `flight_id` bigint unsigned NOT NULL,
  `departure_id` bigint unsigned NOT NULL,
  `total_seat` int NOT NULL,
  `blocked_seat` int NOT NULL DEFAULT '0',
  `used_seat` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seat_allocations_flight_id_departure_id_unique` (`flight_id`,`departure_id`),
  KEY `seat_allocations_departure_id_foreign` (`departure_id`),
  CONSTRAINT `seat_allocations_departure_id_foreign` FOREIGN KEY (`departure_id`) REFERENCES `paket_departures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seat_allocations_flight_id_foreign` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seat_allocations`
--

LOCK TABLES `seat_allocations` WRITE;
/*!40000 ALTER TABLE `seat_allocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `seat_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'TEST','superadmin@umrahcore.test',NULL,'$2y$12$7JcmWChT9/PBcN9.Ykz0N.2Sb3LW2qxcibbInwFOto4RjoQzK6LxO',NULL,'2026-03-27 16:34:19','2026-03-28 07:29:58',NULL),(2,'ADMIN PUSAT','adminpusat@umrahcore.test',NULL,'$2y$12$jKotttNs0FEUfd0SD.R8SuwV6wnI9qKvs9PWYhZoQuY89HoWkMeXW',NULL,'2026-03-27 16:34:19','2026-03-27 16:34:19',NULL),(3,'ADMIN CABANG HQ','admincabang@umrahcore.test',NULL,'$2y$12$ZqG7Xz.GjWdRdLQG5lJxEOcFu7nr0i1FvrfzL9c2eZrC4E2riSwJ.',NULL,'2026-03-27 16:34:19','2026-03-27 16:34:19',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visa_order_documents`
--

DROP TABLE IF EXISTS `visa_order_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visa_order_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visa_order_id` bigint unsigned NOT NULL,
  `visa_order_traveler_id` bigint unsigned DEFAULT NULL,
  `document_type` enum('ktp','kk','passport','photo','ticket','hotel_booking','transport_booking','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visa_order_documents_visa_order_id_foreign` (`visa_order_id`),
  KEY `visa_order_documents_visa_order_traveler_id_foreign` (`visa_order_traveler_id`),
  KEY `visa_order_documents_verified_by_foreign` (`verified_by`),
  KEY `visa_order_documents_document_type_index` (`document_type`),
  KEY `visa_order_documents_is_verified_index` (`is_verified`),
  CONSTRAINT `visa_order_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visa_order_documents_visa_order_id_foreign` FOREIGN KEY (`visa_order_id`) REFERENCES `visa_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visa_order_documents_visa_order_traveler_id_foreign` FOREIGN KEY (`visa_order_traveler_id`) REFERENCES `visa_order_travelers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visa_order_documents`
--

LOCK TABLES `visa_order_documents` WRITE;
/*!40000 ALTER TABLE `visa_order_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `visa_order_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visa_order_notes`
--

DROP TABLE IF EXISTS `visa_order_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visa_order_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visa_order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `note_type` enum('internal','customer','system') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visa_order_notes_visa_order_id_foreign` (`visa_order_id`),
  KEY `visa_order_notes_user_id_foreign` (`user_id`),
  KEY `visa_order_notes_note_type_index` (`note_type`),
  CONSTRAINT `visa_order_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visa_order_notes_visa_order_id_foreign` FOREIGN KEY (`visa_order_id`) REFERENCES `visa_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visa_order_notes`
--

LOCK TABLES `visa_order_notes` WRITE;
/*!40000 ALTER TABLE `visa_order_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `visa_order_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visa_order_travelers`
--

DROP TABLE IF EXISTS `visa_order_travelers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visa_order_travelers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visa_order_id` bigint unsigned NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relationship` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_main_applicant` tinyint(1) NOT NULL DEFAULT '0',
  `place_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Indonesia',
  `nik` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_issue_date` date DEFAULT NULL,
  `passport_expiry_date` date DEFAULT NULL,
  `passport_issue_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visa_order_travelers_visa_order_id_foreign` (`visa_order_id`),
  KEY `visa_order_travelers_full_name_index` (`full_name`),
  KEY `visa_order_travelers_passport_number_index` (`passport_number`),
  CONSTRAINT `visa_order_travelers_visa_order_id_foreign` FOREIGN KEY (`visa_order_id`) REFERENCES `visa_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visa_order_travelers`
--

LOCK TABLES `visa_order_travelers` WRITE;
/*!40000 ALTER TABLE `visa_order_travelers` DISABLE KEYS */;
/*!40000 ALTER TABLE `visa_order_travelers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visa_orders`
--

DROP TABLE IF EXISTS `visa_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visa_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `visa_product_id` bigint unsigned NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `total_travelers` int unsigned NOT NULL DEFAULT '1',
  `departure_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `departure_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_status` enum('draft','pending','waiting_documents','waiting_payment','processing','submitted','approved','rejected','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_status` enum('unpaid','partial','paid','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `admin_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `customer_note` text COLLATE utf8mb4_unicode_ci,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visa_orders_order_number_unique` (`order_number`),
  KEY `visa_orders_user_id_foreign` (`user_id`),
  KEY `visa_orders_visa_product_id_foreign` (`visa_product_id`),
  KEY `visa_orders_order_status_index` (`order_status`),
  KEY `visa_orders_payment_status_index` (`payment_status`),
  KEY `visa_orders_departure_date_index` (`departure_date`),
  CONSTRAINT `visa_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visa_orders_visa_product_id_foreign` FOREIGN KEY (`visa_product_id`) REFERENCES `visa_products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visa_orders`
--

LOCK TABLES `visa_orders` WRITE;
/*!40000 ALTER TABLE `visa_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `visa_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visa_payments`
--

DROP TABLE IF EXISTS `visa_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visa_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visa_order_id` bigint unsigned NOT NULL,
  `payment_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('bank_transfer','cash','gateway','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('pending','paid','failed','expired','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `confirmed_by` bigint unsigned DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visa_payments_payment_number_unique` (`payment_number`),
  KEY `visa_payments_visa_order_id_foreign` (`visa_order_id`),
  KEY `visa_payments_confirmed_by_foreign` (`confirmed_by`),
  KEY `visa_payments_payment_status_index` (`payment_status`),
  KEY `visa_payments_payment_method_index` (`payment_method`),
  CONSTRAINT `visa_payments_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visa_payments_visa_order_id_foreign` FOREIGN KEY (`visa_order_id`) REFERENCES `visa_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visa_payments`
--

LOCK TABLES `visa_payments` WRITE;
/*!40000 ALTER TABLE `visa_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `visa_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visa_products`
--

DROP TABLE IF EXISTS `visa_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visa_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `product_type` enum('visa_only','visa_bundle','add_on') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visa_only',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `promo_price` decimal(15,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `requirements` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visa_products_code_unique` (`code`),
  UNIQUE KEY `visa_products_slug_unique` (`slug`),
  KEY `visa_products_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `visa_products_product_type_index` (`product_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visa_products`
--

LOCK TABLES `visa_products` WRITE;
/*!40000 ALTER TABLE `visa_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `visa_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visa_status_histories`
--

DROP TABLE IF EXISTS `visa_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visa_status_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visa_order_id` bigint unsigned NOT NULL,
  `from_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `changed_by` bigint unsigned DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visa_status_histories_visa_order_id_foreign` (`visa_order_id`),
  KEY `visa_status_histories_changed_by_foreign` (`changed_by`),
  KEY `visa_status_histories_to_status_index` (`to_status`),
  KEY `visa_status_histories_changed_at_index` (`changed_at`),
  CONSTRAINT `visa_status_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visa_status_histories_visa_order_id_foreign` FOREIGN KEY (`visa_order_id`) REFERENCES `visa_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visa_status_histories`
--

LOCK TABLES `visa_status_histories` WRITE;
/*!40000 ALTER TABLE `visa_status_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `visa_status_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('fixed','percent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `max_discount` decimal(15,2) DEFAULT NULL,
  `quota` int DEFAULT NULL,
  `used` int NOT NULL DEFAULT '0',
  `expired_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-28  0:31:48
