-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: mtk_ti
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
-- Table structure for table `administrativos`
--

DROP TABLE IF EXISTS `administrativos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administrativos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `dni` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `profesion` varchar(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `administrativos_dni_unique` (`dni`),
  KEY `administrativos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `administrativos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrativos`
--

LOCK TABLES `administrativos` WRITE;
/*!40000 ALTER TABLE `administrativos` DISABLE KEYS */;
/*!40000 ALTER TABLE `administrativos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracions`
--

DROP TABLE IF EXISTS `configuracions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `direccion` text NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `web` varchar(255) DEFAULT NULL,
  `logo` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracions`
--

LOCK TABLES `configuracions` WRITE;
/*!40000 ALTER TABLE `configuracions` DISABLE KEYS */;
INSERT INTO `configuracions` VALUES (1,'MEGATK','Soluciones Tecnologicas para tu Empresa','Tegicugalpa','+504-22694416','soporte@megatk.com','www.megatk.net','uploads/logos/1746741914_logomega.png','2025-05-08 22:05:14','2025-05-08 22:05:14');
/*!40000 ALTER TABLE `configuracions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `connections`
--

DROP TABLE IF EXISTS `connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `port_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(10) DEFAULT NULL,
  `sw` varchar(10) DEFAULT NULL,
  `equipos_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `connections_port_id_foreign` (`port_id`),
  KEY `connections_equipos_id_foreign` (`equipos_id`),
  CONSTRAINT `connections_equipos_id_foreign` FOREIGN KEY (`equipos_id`) REFERENCES `equipos` (`id`),
  CONSTRAINT `connections_port_id_foreign` FOREIGN KEY (`port_id`) REFERENCES `ports` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connections`
--

LOCK TABLES `connections` WRITE;
/*!40000 ALTER TABLE `connections` DISABLE KEYS */;
INSERT INTO `connections` VALUES (3,97,'PP-A-5',NULL,123,'2025-04-30 21:36:40','2025-04-30 21:36:40'),(4,122,'PP-F-2',NULL,124,'2025-04-30 21:37:14','2025-05-14 18:10:20'),(5,123,'PP-F-3',NULL,125,'2025-04-30 21:37:39','2025-05-14 18:10:07'),(6,124,'PP-F-4',NULL,126,'2025-04-30 21:38:00','2025-05-14 18:09:50'),(7,126,'PP-F-6',NULL,128,'2025-04-30 21:39:13','2025-05-14 18:09:31'),(8,127,'PP-F-7',NULL,129,'2025-04-30 21:39:39','2025-05-14 18:09:12'),(9,128,'PP-F-8',NULL,130,'2025-04-30 21:39:59','2025-05-14 18:08:57'),(10,130,'PP-F-10',NULL,131,'2025-04-30 21:40:16','2025-05-14 18:08:40'),(11,131,'PP-F-11',NULL,132,'2025-04-30 21:40:32','2025-05-14 18:08:07'),(12,137,'PP-F-17',NULL,135,'2025-04-30 21:40:54','2025-05-14 18:06:53'),(13,27,'PP-B-3',NULL,134,'2025-04-30 21:41:32','2025-05-14 18:06:39'),(14,116,'PP-E-20',NULL,137,'2025-04-30 21:42:16','2025-05-14 18:06:26'),(15,37,'PP-B-13',NULL,161,'2025-05-08 20:39:01','2025-05-14 18:06:03'),(16,117,'PP-E-21',NULL,123,'2025-05-08 21:34:41','2025-05-14 18:05:30'),(17,36,'PP-B-12',NULL,166,'2025-05-08 21:55:58','2025-05-14 18:04:26'),(18,1,'PP-A-1',NULL,93,'2025-05-08 21:58:44','2025-05-14 16:29:35'),(19,47,'PP-B-23',NULL,94,'2025-05-12 16:08:10','2025-05-14 18:04:13'),(20,21,'PP-A-21',NULL,114,'2025-05-14 22:19:52','2025-05-14 22:19:52'),(21,19,'PP-A-19',NULL,96,'2025-05-14 22:21:41','2025-05-14 22:21:41'),(22,49,'PP-C-1','SW-B-3',167,'2025-05-15 19:01:52','2025-05-15 19:01:52'),(23,55,'PP-C-7','SW-B-7',168,'2025-05-15 19:02:45','2025-05-15 19:02:45'),(24,56,'PP-C-8','SW-B-9',169,'2025-05-15 19:03:26','2025-05-15 19:03:26'),(25,57,'PP-C-9','SW-B-11',170,'2025-05-15 19:05:11','2025-05-15 19:05:11'),(26,84,'PP-D-12','SW-B-12',171,'2025-05-15 19:05:51','2025-05-15 19:05:51'),(27,66,'PP-C-18','SW-B-17',172,'2025-05-15 19:06:16','2025-05-15 19:06:16'),(28,74,'PP-D-2','SW-B-2',173,'2025-05-15 19:06:47','2025-05-15 19:06:47'),(29,83,'PP-D-11','SW-B-6',174,'2025-05-15 19:07:33','2025-05-15 19:07:33'),(30,82,'PP-D-10','SW-B-8',175,'2025-05-15 19:07:59','2025-05-15 19:07:59'),(31,67,'PP-C-19','SW-B-19',176,'2025-05-15 19:08:30','2025-05-15 19:08:30'),(32,68,'PP-C-20','SW-B-21',177,'2025-05-15 19:08:53','2025-05-15 19:08:53'),(33,86,'PP-D-14','SW-B-16',178,'2025-05-15 19:09:21','2025-05-15 19:15:17'),(34,81,'PP-D-9','SW-B-10',181,'2025-05-15 21:57:45','2025-05-15 21:57:45'),(41,1,'PP-A-1','SW-A-1',93,'2025-05-16 21:50:31','2025-05-16 21:50:31'),(42,2,'PP-A-2','SW-A-2',182,'2025-05-16 21:52:34','2025-05-16 21:52:34'),(43,4,'PP-A-4','SW-A-4',182,'2025-05-16 21:52:52','2025-05-16 21:52:52'),(44,5,'PP-A-5','SW-A-5',182,'2025-05-16 21:53:11','2025-05-16 21:53:11'),(45,6,'PP-A-6','SW-A-6',182,'2025-05-16 21:53:30','2025-05-16 21:53:30'),(46,7,'PP-A-7','SW-A-7',182,'2025-05-16 21:53:51','2025-05-16 21:53:51'),(47,8,'PP-A-8','SW-A-8',182,'2025-05-16 21:54:13','2025-05-16 21:54:13'),(48,9,'PP-A-9','SW-A-9',182,'2025-05-16 21:54:27','2025-05-16 21:54:27'),(49,10,'PP-A-10','SW-A-10',182,'2025-05-16 21:54:50','2025-05-16 21:54:50'),(50,11,'PP-A-11','SW-A-11',182,'2025-05-16 21:55:06','2025-05-16 21:55:06'),(51,12,'PP-A-12','SW-A-12',182,'2025-05-16 21:55:19','2025-05-16 21:55:19'),(52,13,'PP-A-13','SW-A-13',182,'2025-05-16 21:55:34','2025-05-16 21:55:34'),(53,14,'PP-A-14','SW-A-14',182,'2025-05-16 21:55:49','2025-05-16 21:55:49'),(54,15,'PP-A-15','SW-A-15',182,'2025-05-16 21:56:09','2025-05-16 21:56:09'),(55,16,'PP-A-16','SW-A-16',182,'2025-05-16 21:56:24','2025-05-16 21:56:24'),(56,17,'PP-A-17','SW-A-17',182,'2025-05-16 21:56:40','2025-05-16 21:56:40'),(57,18,'PP-A-18','SW-A-18',182,'2025-05-16 21:56:54','2025-05-16 21:56:54'),(58,19,'PP-A-19','SW-A-19',182,'2025-05-16 21:57:09','2025-05-16 21:57:09'),(59,20,'PP-A-20','SW-A-20',182,'2025-05-16 21:57:25','2025-05-16 21:57:25'),(60,21,'PP-A-21','SW-A-21',182,'2025-05-16 21:57:43','2025-05-16 21:57:43'),(61,23,'PP-A-23','SW-A-23',182,'2025-05-16 21:58:00','2025-05-16 21:58:00'),(62,24,'PP-A-24','SW-A-24',182,'2025-05-16 21:58:15','2025-05-16 21:58:15'),(63,26,'PP-B-2','SW-A-26',102,'2025-05-17 14:47:19','2025-05-17 14:47:19');
/*!40000 ALTER TABLE `connections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departamentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos`
--

LOCK TABLES `departamentos` WRITE;
/*!40000 ALTER TABLE `departamentos` DISABLE KEYS */;
INSERT INTO `departamentos` VALUES (1,'ADMINISTRACIÓN',NULL,'2025-05-09 23:02:03'),(2,'BODEGA',NULL,NULL),(3,'CLINICA',NULL,NULL),(4,'EXHIBICIÓN',NULL,'2025-05-08 22:43:36'),(5,'GERENCIA',NULL,NULL),(6,'KREATIVA',NULL,NULL),(7,'MEDITEK',NULL,NULL),(8,'PRODUCCIÓN',NULL,NULL),(9,'PROYECTOS',NULL,NULL),(10,'RECEPCIÓN',NULL,NULL),(11,'SALA DE JUNTAS',NULL,NULL),(12,'SOPORTE TECNICO',NULL,NULL),(13,'VENTAS',NULL,NULL),(14,'VENTAS CALLE',NULL,NULL),(15,'VENTAS MEDITEK',NULL,NULL),(16,'SPS',NULL,NULL),(17,'SIN IDENTIFICAR',NULL,NULL);
/*!40000 ALTER TABLE `departamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispositivos`
--

DROP TABLE IF EXISTS `dispositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dispositivos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mac` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `ultima_conexion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dispositivos_mac_unique` (`mac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispositivos`
--

LOCK TABLES `dispositivos` WRITE;
/*!40000 ALTER TABLE `dispositivos` DISABLE KEYS */;
/*!40000 ALTER TABLE `dispositivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `dni` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `reloj` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `departamento_id` bigint(20) unsigned NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `usuario_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empleados_departamento_id_foreign` (`departamento_id`),
  KEY `empleados_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `empleados_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `empleados_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,'CELVYN','LOZANO','0801-1979-00316','9948-8205','14','1979-01-18',12,'Activo',2,NULL,'2025-04-14 22:42:23'),(2,'MYRTI','SAUCEDA','801','9942-5434','1','2025-04-01',1,'Activo',3,NULL,'2025-04-15 13:26:46'),(3,'ELISA','RODRIGUEZ','801','9509-3275','3','0000-00-00',1,'Activo',4,NULL,NULL),(4,'DARCY','VASQUEZ','801','9872-3196','4','0000-00-00',1,'Activo',5,NULL,NULL),(5,'KENIA','CISNADO','801','3146-7883','5','0000-00-00',1,'Activo',6,NULL,NULL),(6,'SARA','FLORES','801','131231321','6','0000-00-00',9,'Activo',7,NULL,NULL),(7,'DIANA','COLINDRES','801','87654321','7','0000-00-00',6,'Activo',8,NULL,NULL),(8,'DANIELA','CHINCHILLA','801','3287-4994','8','0000-00-00',6,'Activo',9,NULL,NULL),(9,'DAVID','ZUNIGA','801','3153-5565','9','0000-00-00',6,'Activo',10,NULL,NULL),(10,'ANTONIO','CRUZ','801','3330-6136','10','0000-00-00',6,'Activo',11,NULL,NULL),(11,'DARWIN','RIVERA','801','87654321','11','0000-00-00',2,'Activo',12,NULL,NULL),(12,'MARIO','HERNANDEZ','801','8736-7933','12','0000-00-00',2,'Activo',13,NULL,NULL),(13,'YAJAIRA','ALVARADO','801','9470-1896','13','0000-00-00',13,'Activo',14,NULL,NULL),(14,'MANUEL','GARCIA','801','9972-3670','14','0000-00-00',13,'Activo',15,NULL,NULL),(15,'DAVID','MIDENCE','801','9519-1087','15','0000-00-00',13,'Activo',16,NULL,NULL),(16,'LUIS','DIAZ','801','9509-3267','16','0000-00-00',12,'Activo',17,NULL,NULL),(17,'HARLING','MARADIAGA','0801-2005-22069','32132859','146','2005-12-12',12,'Activo',18,NULL,'2025-05-16 21:48:40'),(18,'KEVIN','GODOY','801','9816-3189','18','0000-00-00',12,'Activo',19,NULL,NULL),(19,'LAPTOP','SOPORTE','801','12345678','19','0000-00-00',12,'Activo',20,NULL,NULL),(20,'Noely','Fuentes','801','12345678','20','0000-00-00',7,'Activo',21,NULL,NULL),(21,'FANNY','NUÑEZ','801','9738-6374','21','0000-00-00',7,'Activo',22,NULL,NULL),(22,'Ventas','Calle','801','12345678','22','0000-00-00',7,'Activo',23,NULL,NULL),(23,'ESAU','AGUILERA','801','9994-6360','23','0000-00-00',12,'Activo',24,NULL,NULL),(24,'Jeovanny','Romero','801','12345678','24','0000-00-00',6,'Activo',25,NULL,NULL),(25,'ALEJANDRA','SANCHEZ','801','8738-2352','25','0000-00-00',12,'Activo',26,NULL,NULL),(27,'ROLANDO','CACERES','801','9450-1578','26','0000-00-00',7,'Activo',27,NULL,NULL),(28,'REINA','SALGADO','801','3200-0290','27','0000-00-00',1,'Activo',28,NULL,NULL),(29,'PLANTA','TELEFONICA','801','12121','28','0000-00-00',12,'Activo',29,NULL,NULL),(30,'PENDIENTE','IDENTIFICAR','801','telefono','29','0000-00-00',12,'Activo',30,NULL,NULL),(31,'DENNIS','GARCIA','801','99887766','30','0000-00-00',16,'Activo',31,NULL,NULL),(32,'JENNIFER','JOVEL','801','9651-0062','31','0000-00-00',5,'Activo',32,NULL,NULL),(33,'LUIS','MORAN','801','9985-4193','32','0000-00-00',5,'Activo',33,NULL,NULL),(34,'VANESA','MORAN','801','9539-4763','33','0000-00-00',16,'Activo',34,NULL,NULL),(35,'MAURICIO','CASTILLO','801','9992-2928','34','0000-00-00',12,'Activo',35,NULL,NULL),(36,'MIGUEL','MARQUEZ','801','87654321','35','0000-00-00',16,'Activo',36,NULL,NULL),(37,'rrrrrr','rrrrrrrrrrrr','123123','123123','123','2025-04-02',1,'Activo',37,'2025-04-15 22:08:52','2025-04-15 22:08:52'),(38,'EQUIPO','ST','1111','111','0','2025-04-16',12,'Activo',38,'2025-04-23 14:27:55','2025-04-23 14:31:09'),(39,'EQUIPO','ADMINISTRACION','08019010271020','22694416','0','2025-04-24',1,'Activo',39,'2025-04-24 20:45:15','2025-04-24 20:45:15'),(40,'EQUIPO','MEDITEK','0801901204524','23423','0','2025-04-24',7,'Activo',40,'2025-04-24 20:46:04','2025-04-24 20:46:04'),(41,'CLAUDETH','APL','231','234','156','2025-04-10',1,'Activo',41,'2025-04-24 20:57:02','2025-04-24 20:57:02'),(42,'ELSY','APL','12312','123123','1111','2025-04-17',6,'Activo',42,'2025-04-24 21:11:33','2025-04-24 21:11:33'),(43,'CAMARAS','TGU','1111','1','1','2025-04-29',1,'Activo',43,'2025-04-29 15:18:40','2025-04-29 15:18:40');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipos`
--

DROP TABLE IF EXISTS `equipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `mac_address` varchar(255) DEFAULT NULL,
  `puerto_patch` varchar(255) DEFAULT NULL,
  `empleado_id` bigint(20) unsigned DEFAULT NULL,
  `marca_id` bigint(20) unsigned DEFAULT NULL,
  `modelo_id` int(11) DEFAULT NULL,
  `antivirus` enum('Activo','Inactivo') NOT NULL DEFAULT 'Inactivo',
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `procesador` varchar(255) DEFAULT NULL,
  `ram` varchar(255) DEFAULT NULL,
  `almacenamiento` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipos_nombre_unique` (`nombre`),
  KEY `equipos_empleado_id_foreign` (`empleado_id`),
  KEY `equipos_marca_id_foreign` (`marca_id`),
  CONSTRAINT `equipos_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL,
  CONSTRAINT `equipos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipos`
--

LOCK TABLES `equipos` WRITE;
/*!40000 ALTER TABLE `equipos` DISABLE KEYS */;
INSERT INTO `equipos` VALUES (80,'Mikrotik','12312312','192.168.10.1','64:D1:54:CF:11:E2','sw-1',38,2,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:18:17'),(81,'CISCO','12312313','192.168.10.2','C8:00:84:8B:87:B9','sw-2',38,3,NULL,'Activo','Inactivo','i5','5 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:18:08'),(82,'192.168.10.4','12312314','192.168.10.4','CC:2D:E0:6D:40:49','sw-3',38,2,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:17:59'),(83,'192.168.10.9','12312315','192.168.10.9','30:B5:C2:C0:41:DB','rev',38,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:17:54'),(84,'SRVDCMTK_02','12312316','192.168.10.11','02:42:54:52:42:54','sw-5',38,4,NULL,'Activo','Inactivo','i9','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:17:47'),(85,'SRV-WEB01','12312317','192.168.10.12','00:15:5D:0A:0B:15','sw-6',38,7,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:17:40'),(86,'MEGAMARCACIONES','12312318','192.168.10.14','B8:97:5A:80:52:CC','sw-7',38,8,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:17:33'),(87,'BHC25-DDB9FE','12312319','192.168.10.20','00:20:6B:DD:B9:FE','IMP',39,9,NULL,'Activo','Activo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:10:21'),(88,'192.168.10.21','12312320','192.168.10.21','00:20:6B:D3:54:3D','IMP',39,9,NULL,'Activo','Activo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:09:57'),(89,'EPSON9E898F','12312321','192.168.10.23','64:C6:D2:9E:89:8F','sw-10',38,10,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:09:44'),(90,'BH402061A6C9','12312322','192.168.10.24','00:21:B7:61:A6:C9','IMP',40,9,NULL,'Activo','Activo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:09:31'),(93,'GERENCIA','12312325','192.168.10.53','CC:96:E5:04:61:B5','PC',33,5,NULL,'Activo','Activo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:09:04'),(94,'MTK-SJ','SALA DE JUNTAS','192.168.10.59','48:4D:7E:E8:BC:9D','PC',39,5,NULL,'Activo','Activo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 16:07:25'),(95,'MTK-ADM-ERODRIG','12312327','192.168.10.60','F4:EE:08:D9:88:F3','sw-16',3,6,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:08:40'),(96,'STTG-CLOZANO','12312328','192.168.10.65','12:22:22:22:22:22','sw-17',1,1,NULL,'Activo','Inactivo','Activo','20 GB','256 SSD','0000-00-00 00:00:00','2025-05-12 22:08:37'),(97,'KRTG-DCOLINDRES','12312329','192.168.10.71','B0:6E:BF:AD:3F:BE','sw-18',7,6,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:08:32'),(98,'AJZ04-PC','12312330','192.168.10.72','F8:75:A4:0C:2F:A7','sw-19',9,6,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:08:10'),(99,'KTGU-ECOLINDRES','12312331','192.168.10.74','F8:75:A4:0C:32:90','sw-20',41,5,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:08:06'),(100,'192.168.10.81','12312332','192.168.10.81','B8:97:5A:B1:D8:71','sw-21',1,1,NULL,'Activo','Inactivo','Activo','24 GB','260 SSD','0000-00-00 00:00:00','2025-05-12 22:08:00'),(101,'gestionmtk','12312333','192.168.10.86','D4:81:D7:80:51:6E','sw-22',16,5,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:07:50'),(102,'MTK-VC-YALVARADO','12312334','192.168.10.88','00:E0:4C:68:03:67','sw-23',13,6,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:07:40'),(103,'192.168.10.93','12312335','192.168.10.93','50:91:E3:E1:D2:6F','sw-24',11,6,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:07:32'),(104,'DESKTOP-B3MIODR','12312336','192.168.10.95','00:E8:01:35:93:41','sw-25',1,1,NULL,'Activo','Inactivo','Activo','28 GB','264 SSD','0000-00-00 00:00:00','2025-05-12 22:07:27'),(105,'192.168.10.122','ASISTENCIA','192.168.10.122','00:22:CA:8B:66:38','sw-26',38,11,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:07:22'),(106,'ANVIZ ASISTENCIA','12312338','192.168.10.123','00:1B:09:02:D6:F0','sw-27',38,11,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:07:16'),(107,'POS_1','12312339','192.168.10.150','38:EF:E3:CD:0D:4D','sw-28',39,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:07:07'),(108,'POS_2','12312340','192.168.10.151','38:EF:E3:7C:82:5B','sw-29',39,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:07:01'),(109,'MTK-VC-MGARCIA','12312341','192.168.10.162','B8:97:5A:05:DA:CB','sw-30',14,8,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:06:56'),(110,'COLOCAR','12312342','192.168.10.201','EC:8E:B5:45:B7:C9','sw-31',27,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:06:11'),(111,'192.168.10.202','REVISAR','192.168.10.202','8C:04:BA:2B:8A:99','sw-32',39,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:46'),(112,'SRVWEB01','12312344','192.168.10.204','00:15:5D:0A:0B:11','sw-33',38,7,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:40'),(113,'TGKR-SPAZ','12312345','192.168.10.205','00:3E:E1:C1:DC:D1','sw-34',42,13,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:37'),(114,'STTG-HMARADIAGA','12312346','192.168.10.206','00:25:AB:0F:19:8C','sw-35',17,4,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:33'),(115,'STTG-GPAZ','12312347','192.168.10.208','64:00:6A:09:49:76','sw-36',25,5,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:25'),(116,'192.168.10.209','12312348','192.168.10.209','A0:B3:CC:23:85:CF','sw-37',37,4,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:22'),(117,'192.168.10.212','12312349','192.168.10.212','84:C2:E4:17:E7:90','sw-38',37,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:17'),(118,'DESKTOP-2KOKM0A','12312350','192.168.10.214','44:45:6F:01:64:A5','sw-39',37,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:14'),(119,'DESKTOP-MV466C6','12312351','192.168.10.217','78:24:AF:3C:7F:08','sw-40',37,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:05:10'),(120,'DESKTOP-ID96K88','12312352','192.168.10.222','B4:45:06:96:1E:DA','sw-41',37,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:04:56'),(121,'192.168.10.240','12312353','192.168.10.240','30:85:A9:AA:B4:54','sw-42',37,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:04:49'),(122,'192.168.10.241','12312354','192.168.10.241','00:17:61:12:27:5A','sw-43',37,1,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 22:04:12'),(123,'ENTRADA SOPORTE','12345678','192.168.3.32','00:08:25:6A:4E:DE','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(124,'MEDITEK','12345679','192.168.3.23','54:C4:15:3E:61:6D','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(125,'EXHIBICION','12345680','192.168.3.15','C0:56:E3:F4:D0:31','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(126,'VENTAS','12345681','192.168.3.11','28:57:BE:3E:CA:D2','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(127,'CLINICA','12345682','192.168.3.12','COLOCAR','CAMARA',43,14,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 15:25:01'),(128,'PASILLO MEDITEK','12345683','192.168.3.24','C0:56:E3:F4:CF:E4','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(129,'ENTRADA MEGA','12345684','192.168.3.12','C0:56:E3:F4:CF:F6','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(130,'COCINA','12345685','192.168.3.13','C0:56:E3:F3:5F:27','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(131,'KREATIVA','12345686','192.168.3.35','C0:56:E3:F1:3C:AE','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(132,'ADMINISTRACION','12345687','192.168.3.18','18:68:CB:10:6B:98','CAMARA',43,14,4,'Activo','Activo','Activo','4 GB','240 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(133,'SALA JUNTAS','12345688','COLOCAR','COLOCAR','CAMARA',43,14,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','0000-00-00 00:00:00','2025-05-12 15:24:45'),(134,'VENTAS CALLE','12345689','192.168.3.19','64:DB:8B:4A:E3:19','CAMARA',43,14,4,'Activo','Activo','Activo','5 GB','241 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(135,'BODEGA','12345690','192.168.3.14','C0:56:E3:F4:CF:DB','CAMARA',43,14,4,'Activo','Activo','Activo','5 GB','241 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(136,'NVR','12345691','192.168.3.3','68:6D:BC:EA:80:A3','CAMARA',43,14,4,'Activo','Activo','Activo','6 GB','242 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(137,'CALLE','12345691','172.168.1.1','COLOCAR','CAMARAS',43,14,4,'Activo','Activo','Activo','7 GB','243 SSD','0000-00-00 00:00:00','0000-00-00 00:00:00'),(161,'K-GODOY','11111','192.168.10.89','B8:97:5A:05:D8:91','PC',18,8,NULL,'Activo','Activo','Inactivo','5 GB','500 HDD','2025-05-08 20:29:22','2025-05-08 20:29:22'),(162,'SALA DE JUNTAS','143011001710337','192.168.10.30','00:22:CA:89:AE:33','BIOMETRICO',38,11,6,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-08 20:46:28','2025-05-08 20:46:28'),(165,'ACCESO_BODEGA','119030000115190122','192.168.10.32','00:22:CA:88:66:32','BIOMETRICO',38,11,5,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-08 20:48:38','2025-05-12 22:04:53'),(166,'CAJA REVISAR','1111','192.168.3.REVISA','REV','PC',5,5,NULL,'Activo','Inactivo','i5','4 GB','240 SSD','2025-05-08 21:55:10','2025-05-12 16:15:42'),(167,'TGU-GRE-TEL-9010','111111','192.168.1.210','1','PC',33,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 17:55:45','2025-05-15 17:55:45'),(168,'TGU-ADM-TEL-9008','1111111','192.168.2.208','1','PC',2,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 17:57:18','2025-05-15 17:57:18'),(169,'TGU-ADM-TEL-9009','111','192.168.2.209','1','PC',3,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:00:08','2025-05-15 18:00:08'),(170,'TGU-ADM-TEL-9006','1111','192.168.2.206','1','PC',4,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:01:17','2025-05-15 18:01:17'),(171,'TGU-ADM-TEL-9025','11111','192.168.2.225','1','PC',5,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:03:27','2025-05-15 18:03:27'),(172,'TGU-BG-TEL-9023','11111','192.168.1.223','1','PC',11,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:05:27','2025-05-15 18:05:27'),(173,'TGU-V-TEL-9005','111','192.168.1.205','1','PC',13,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:08:12','2025-05-15 18:08:12'),(174,'TGU-V-TEL-9022','111','192.168.2.222','1','PC',15,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:09:16','2025-05-15 18:09:16'),(175,'TGU-V-TEL-9013','1111','192.168.2.213','1','PC',14,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:11:08','2025-05-15 18:11:08'),(176,'TGU-ST-TEL-9004','111','192.168.1.204','1','PC',1,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:12:17','2025-05-15 18:12:17'),(177,'TGU-ST-TEL-9031','111','192.168.2.231','1','PC',18,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:13:38','2025-05-15 18:13:38'),(178,'TGU-ST-TEL-9001','1111','192.168.2.198','1','PC',25,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:14:35','2025-05-15 18:14:35'),(179,'TGU-V-TEL-9017','1111','192.168.2.217','1','PC',23,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 18:15:52','2025-05-15 18:15:52'),(180,'TGU-SRV-TEL-01','1111','192.168.2.197','1','PC',38,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 19:12:27','2025-05-15 19:12:27'),(181,'TGU-MDK-TEL-9024','1111','192.168.2.224','1','PC',40,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-15 21:56:36','2025-05-15 21:56:36'),(182,'IDENTIFICAR','000000000000000000','0000000000','0000000000000','PC',38,1,NULL,'Activo','Activo','Activo','4 GB','240 SSD','2025-05-16 21:52:12','2025-05-16 21:52:12');
/*!40000 ALTER TABLE `equipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marcas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'EVOLIS','2025-04-07 20:41:08','2025-05-09 23:00:03'),(2,'MIKROTIK','2025-04-23 14:32:07','2025-04-23 14:32:07'),(3,'CISCO','2025-04-23 14:34:37','2025-04-23 14:34:37'),(4,'HP','2025-04-23 21:53:29','2025-04-23 21:53:29'),(5,'DELL','2025-04-23 21:53:37','2025-04-23 21:53:37'),(6,'ASUS','2025-04-23 21:53:51','2025-04-23 21:53:51'),(7,'HYPER-V','2025-04-23 21:54:16','2025-04-23 21:54:16'),(8,'CLON','2025-04-23 21:55:29','2025-04-23 21:55:29'),(9,'KONICA','2025-04-24 20:46:46','2025-04-24 20:46:46'),(10,'EPSON','2025-04-24 20:46:55','2025-04-24 20:46:55'),(11,'ANVIZ','2025-04-24 20:49:27','2025-04-24 20:49:27'),(12,'INGENICO','2025-04-24 21:04:09','2025-04-24 21:04:09'),(13,'MAC','2025-04-24 21:12:15','2025-04-24 21:12:15'),(14,'HIKVISION','2025-04-29 15:13:21','2025-04-29 15:13:21');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2014_10_12_100000_create_password_resets_table',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2019_12_14_000001_create_personal_access_tokens_table',1),(6,'2025_02_17_082629_create_configuracions_table',1),(14,'2025_03_16_105935_create_permission_tables',1),(15,'2025_03_16_120318_create_administrativos_table',1),(18,'2025_04_04_160332_create_marcas_table',1),(19,'2025_04_05_123412_create_modelos_table',1),(20,'2025_04_07_081328_create_departamentos_table',1),(21,'2025_04_07_081343_create_empleados_table',1),(23,'2025_04_07_131511_create_equipos_table',2),(24,'2025_04_28_085346_create_panels_table',3),(25,'2025_04_28_125155_create_patch_panels_table',4),(26,'2025_04_28_125212_create_ports_table',4),(27,'2025_04_28_125230_create_connections_table',5),(28,'2025_05_03_090907_create_dispositivos_table',6),(29,'2025_05_15_230414_create_switch_m_i_k_s_table',7),(30,'2025_05_15_235931_create_sw_ports_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modelos`
--

DROP TABLE IF EXISTS `modelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modelos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `marca_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modelos_marca_id_foreign` (`marca_id`),
  CONSTRAINT `modelos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modelos`
--

LOCK TABLES `modelos` WRITE;
/*!40000 ALTER TABLE `modelos` DISABLE KEYS */;
INSERT INTO `modelos` VALUES (1,2,'1100AHX4','2025-04-23 14:35:23','2025-04-23 14:35:23'),(2,3,'SG300-52','2025-04-23 14:36:04','2025-04-23 14:36:04'),(3,2,'RB-950','2025-04-26 17:23:54','2025-05-10 17:16:51'),(4,14,'DS-2CD1121-1','2025-04-29 15:16:51','2025-04-29 15:16:51'),(5,11,'T5-PRO','2025-05-08 20:39:46','2025-05-08 20:39:46'),(6,11,'P7','2025-05-08 20:40:01','2025-05-08 20:40:01'),(7,11,'VF30 PRO','2025-05-08 20:40:14','2025-05-08 20:40:14'),(8,1,'ZENIUS','2025-05-09 23:38:09','2025-05-09 23:38:09'),(9,3,'23423','2025-05-10 00:00:38','2025-05-10 00:00:38');
/*!40000 ALTER TABLE `modelos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `panels`
--

DROP TABLE IF EXISTS `panels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `panels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `monbre` varchar(255) NOT NULL,
  `numero` int(11) NOT NULL,
  `empleado_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `panels_empleado_id_foreign` (`empleado_id`),
  CONSTRAINT `panels_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `panels`
--

LOCK TABLES `panels` WRITE;
/*!40000 ALTER TABLE `panels` DISABLE KEYS */;
/*!40000 ALTER TABLE `panels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
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
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patch_panels`
--

DROP TABLE IF EXISTS `patch_panels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `total_ports` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panels`
--

LOCK TABLES `patch_panels` WRITE;
/*!40000 ALTER TABLE `patch_panels` DISABLE KEYS */;
INSERT INTO `patch_panels` VALUES (1,'PP-A','Tegucigalpa',24,'2025-04-28 19:58:55','2025-04-28 19:58:55'),(2,'PP-B','Tegucigalpa',24,'2025-04-28 19:59:18','2025-04-28 19:59:18'),(3,'PP-C','Tegucigalpa',24,'2025-04-28 19:59:33','2025-04-28 19:59:33'),(4,'PP-D','Tegucigalpa',24,'2025-04-28 19:59:50','2025-04-28 19:59:50'),(5,'PP-E','Tegucigalpa',24,'2025-04-28 20:00:08','2025-04-28 20:00:08'),(6,'PP-F','Tegucigalpa',48,'2025-04-28 20:00:38','2025-04-28 20:00:38'),(7,'REVISAR PATCH','Tegucigalpa',1,'2025-05-08 20:55:15','2025-05-10 18:38:39'),(9,'SW-A','Tegucigalpa',52,'2025-05-15 20:00:29','2025-05-15 20:00:29'),(10,'SW-B','Tegucigalpa',24,'2025-05-15 20:00:41','2025-05-15 20:00:41'),(11,'SW-C','Tegucigalpa',48,'2025-05-15 20:01:04','2025-05-15 20:01:04'),(12,'SW-D','Tegucigalpa',24,'2025-05-15 20:01:29','2025-05-15 20:01:29'),(13,'MIKR-A','Tegucigalpa',13,'2025-05-15 20:02:27','2025-05-15 20:02:27');
/*!40000 ALTER TABLE `patch_panels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'admin.configuracion.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(2,'admin.configuracion.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(3,'admin.roles.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(4,'admin.roles.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(5,'admin.roles.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(6,'admin.roles.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(7,'admin.roles.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(8,'admin.roles.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(9,'admin.roles.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(10,'admin.administrativos.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(11,'admin.administrativos.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(12,'admin.administrativos.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(13,'admin.administrativos.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(14,'admin.administrativos.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(15,'admin.administrativos.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(16,'admin.administrativos.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(17,'admin.marcas.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(18,'admin.marcas.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(19,'admin.marcas.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(20,'admin.marcas.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(21,'admin.marcas.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(22,'admin.marcas.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(23,'admin.marcas.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(24,'admin.modelos.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(25,'admin.modelos.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(26,'admin.modelos.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(27,'admin.modelos.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(28,'admin.modelos.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(29,'admin.modelos.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(30,'admin.modelos.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(31,'admin.departamentos.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(32,'admin.departamentos.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(33,'admin.departamentos.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(34,'admin.departamentos.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(35,'admin.departamentos.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(36,'admin.departamentos.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(37,'admin.departamentos.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(38,'admin.empleados.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(39,'admin.empleados.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(40,'admin.empleados.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(41,'admin.empleados.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(42,'admin.empleados.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(43,'admin.empleados.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(44,'admin.empleados.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(45,'admin.equipos.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(46,'admin.equipos.getModelos','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(47,'admin.equipos.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(48,'admin.equipos.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(49,'admin.equipos.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(50,'admin.equipos.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(51,'admin.equipos.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(52,'admin.equipos.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(53,'admin.equipos.ping','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(54,'admin.equipos.cambiarEstado','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(55,'admin.paneles.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(56,'admin.paneles.getModelos','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(57,'admin.paneles.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(58,'admin.paneles.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(59,'admin.paneles.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(60,'admin.paneles.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(61,'admin.paneles.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(62,'admin.paneles.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(63,'admin.paneles.ping','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(64,'admin.patch_panels.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(65,'admin.patch_panels.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(66,'admin.patch_panels.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(67,'admin.patch_panels.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(68,'admin.patch_panels.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(69,'admin.patch_panels.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(70,'admin.patch_panels.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(71,'admin.switchs.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(72,'admin.switchs.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(73,'admin.switchs.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(74,'admin.switchs.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(75,'admin.switchs.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(76,'admin.switchs.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(77,'admin.switchs.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(78,'admin.switchs.crear_puerto','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(79,'admin.ports.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(80,'admin.ports.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(81,'admin.ports.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(82,'admin.ports.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(83,'admin.ports.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(84,'admin.ports.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(85,'admin.ports.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(86,'admin.connections.index','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(87,'admin.connections.create','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(88,'admin.connections.store','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(89,'admin.connections.show','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(90,'admin.connections.edit','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(91,'admin.connections.update','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(92,'admin.connections.destroy','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(93,'admin.roles.permisos','web','2025-05-18 01:16:24','2025-05-18 01:16:24'),(94,'admin.backups.index','web','2025-05-18 01:34:16','2025-05-18 01:34:16'),(95,'admin.backups.create','web','2025-05-18 01:34:31','2025-05-18 01:34:31'),(96,'admin.backups.download','web','2025-05-18 01:34:49','2025-05-18 01:34:49');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
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
-- Table structure for table `ports`
--

DROP TABLE IF EXISTS `ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `patch_panel_id` bigint(20) unsigned NOT NULL,
  `port_number` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ports_patch_panel_id_foreign` (`patch_panel_id`),
  CONSTRAINT `ports_patch_panel_id_foreign` FOREIGN KEY (`patch_panel_id`) REFERENCES `patch_panels` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=338 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ports`
--

LOCK TABLES `ports` WRITE;
/*!40000 ALTER TABLE `ports` DISABLE KEYS */;
INSERT INTO `ports` VALUES (1,1,1,'0000-00-00 00:00:00','2025-05-10 19:19:22'),(2,1,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,1,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,1,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,1,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,1,6,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,1,7,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,1,8,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,1,9,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(10,1,10,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,1,11,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(12,1,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(13,1,13,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(14,1,14,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(15,1,15,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(16,1,16,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(17,1,17,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(18,1,18,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(19,1,19,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(20,1,20,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(21,1,21,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(22,1,22,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(23,1,23,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(24,1,24,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(25,2,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(26,2,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(27,2,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(28,2,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(29,2,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(30,2,6,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(31,2,7,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(32,2,8,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(33,2,9,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(34,2,10,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(35,2,11,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(36,2,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(37,2,13,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(38,2,14,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(39,2,15,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(40,2,16,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(41,2,17,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(42,2,18,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(43,2,19,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(44,2,20,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(45,2,21,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(46,2,22,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(47,2,23,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(48,2,24,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(49,3,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(50,3,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(51,3,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(52,3,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(53,3,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(54,3,6,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(55,3,7,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(56,3,8,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(57,3,9,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(58,3,10,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(59,3,11,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(60,3,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(61,3,13,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(62,3,14,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(63,3,15,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(64,3,16,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(65,3,17,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(66,3,18,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(67,3,19,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(68,3,20,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(69,3,21,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(70,3,22,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(71,3,23,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(72,3,24,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(73,4,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(74,4,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(75,4,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(76,4,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(77,4,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(78,4,6,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(79,4,7,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(80,4,8,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(81,4,9,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(82,4,10,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(83,4,11,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(84,4,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(85,4,13,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(86,4,14,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(87,4,15,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(88,4,16,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(89,4,17,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(90,4,18,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(91,4,19,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(92,4,20,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(93,4,21,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(94,4,22,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(95,4,23,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(96,4,24,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(97,5,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(98,5,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(99,5,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(100,5,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(101,5,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(102,5,6,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(103,5,7,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(104,5,8,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(105,5,9,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(106,5,10,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(107,5,11,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(108,5,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(109,5,13,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(110,5,14,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(111,5,15,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(112,5,16,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(113,5,17,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(114,5,18,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(115,5,19,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(116,5,20,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(117,5,21,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(118,5,22,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(119,5,23,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(120,5,24,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(121,6,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(122,6,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(123,6,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(124,6,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(125,6,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(126,6,6,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(127,6,7,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(128,6,8,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(129,6,9,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(130,6,10,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(131,6,11,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(132,6,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(133,6,13,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(134,6,14,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(135,6,15,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(136,6,16,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(137,6,17,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(138,6,18,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(139,6,19,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(140,6,20,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(141,6,21,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(142,6,22,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(143,6,23,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(144,6,24,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(145,6,25,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(146,6,26,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(147,6,27,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(148,6,28,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(149,6,29,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(150,6,30,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(151,6,31,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(152,6,32,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(153,6,33,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(154,6,34,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(155,6,35,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(156,6,36,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(157,6,37,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(158,6,38,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(159,6,39,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(160,6,40,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(161,6,41,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(162,6,42,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(163,6,43,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(164,6,44,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(165,6,45,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(166,6,46,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(167,6,47,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(168,6,48,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(169,7,1,'2025-05-08 20:55:55',NULL);
/*!40000 ALTER TABLE `ports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
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
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1),(78,1),(79,1),(80,1),(81,1),(82,1),(83,1),(84,1),(85,1),(86,1),(87,1),(88,1),(89,1),(90,1),(91,1),(92,1),(93,1),(94,1),(95,1),(96,1);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMINISTRADOR','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(2,'ADMINISTRATIVO','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(3,'EMPLEADO','web','2025-05-17 17:56:24','2025-05-17 17:56:24'),(4,'EDITOR','web','2025-05-17 17:56:24','2025-05-17 17:56:24');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sw_ports`
--

DROP TABLE IF EXISTS `sw_ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sw_ports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `switch_m_i_k_s_id` bigint(20) unsigned NOT NULL,
  `sw_port_number` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sw_ports_switch_m_i_k_s_id_foreign` (`switch_m_i_k_s_id`),
  CONSTRAINT `sw_ports_switch_m_i_k_s_id_foreign` FOREIGN KEY (`switch_m_i_k_s_id`) REFERENCES `switch_m_i_k_s` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sw_ports`
--

LOCK TABLES `sw_ports` WRITE;
/*!40000 ALTER TABLE `sw_ports` DISABLE KEYS */;
INSERT INTO `sw_ports` VALUES (1,1,1,'2025-05-16 13:43:50','2025-05-16 13:43:50'),(2,1,2,'2025-05-16 13:43:50','2025-05-16 13:43:50'),(3,1,3,'2025-05-16 13:43:50','2025-05-16 13:43:50'),(4,1,4,'2025-05-16 13:43:50','2025-05-16 13:43:50'),(5,1,5,'2025-05-16 13:43:50','2025-05-16 13:43:50'),(6,1,6,'2025-05-16 13:43:50','2025-05-16 13:43:50'),(7,1,7,'2025-05-16 13:43:51','2025-05-16 13:43:51'),(8,1,8,'2025-05-16 13:43:51','2025-05-16 13:43:51'),(9,1,9,'2025-05-16 13:43:51','2025-05-16 13:43:51'),(10,1,10,'2025-05-16 13:43:51','2025-05-16 13:43:51'),(11,1,11,'2025-05-16 13:43:51','2025-05-16 13:43:51'),(12,1,12,'2025-05-16 13:43:51','2025-05-16 13:43:51'),(13,1,13,'2025-05-16 13:43:51','2025-05-16 13:43:51'),(14,2,1,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(15,2,2,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(16,2,3,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(17,2,4,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(18,2,5,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(19,2,6,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(20,2,7,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(21,2,8,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(22,2,9,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(23,2,10,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(24,2,11,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(25,2,12,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(26,2,13,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(27,2,14,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(28,2,15,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(29,2,16,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(30,2,17,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(31,2,18,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(32,2,19,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(33,2,20,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(34,2,21,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(35,2,22,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(36,2,23,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(37,2,24,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(38,2,25,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(39,2,26,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(40,2,27,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(41,2,28,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(42,2,29,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(43,2,30,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(44,2,31,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(45,2,32,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(46,2,33,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(47,2,34,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(48,2,35,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(49,2,36,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(50,2,37,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(51,2,38,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(52,2,39,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(53,2,40,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(54,2,41,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(55,2,42,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(56,2,43,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(57,2,44,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(58,2,45,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(59,2,46,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(60,2,47,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(61,2,48,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(62,2,49,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(63,2,50,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(64,2,51,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(65,2,52,'2025-05-16 13:44:07','2025-05-16 13:44:07'),(66,3,1,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(67,3,2,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(68,3,3,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(69,3,4,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(70,3,5,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(71,3,6,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(72,3,7,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(73,3,8,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(74,3,9,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(75,3,10,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(76,3,11,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(77,3,12,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(78,3,13,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(79,3,14,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(80,3,15,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(81,3,16,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(82,3,17,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(83,3,18,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(84,3,19,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(85,3,20,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(86,3,21,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(87,3,22,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(88,3,23,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(89,3,24,'2025-05-16 13:44:44','2025-05-16 13:44:44'),(90,4,1,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(91,4,2,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(92,4,3,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(93,4,4,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(94,4,5,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(95,4,6,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(96,4,7,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(97,4,8,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(98,4,9,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(99,4,10,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(100,4,11,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(101,4,12,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(102,4,13,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(103,4,14,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(104,4,15,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(105,4,16,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(106,4,17,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(107,4,18,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(108,4,19,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(109,4,20,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(110,4,21,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(111,4,22,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(112,4,23,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(113,4,24,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(114,4,25,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(115,4,26,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(116,4,27,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(117,4,28,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(118,4,29,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(119,4,30,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(120,4,31,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(121,4,32,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(122,4,33,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(123,4,34,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(124,4,35,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(125,4,36,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(126,4,37,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(127,4,38,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(128,4,39,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(129,4,40,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(130,4,41,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(131,4,42,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(132,4,43,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(133,4,44,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(134,4,45,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(135,4,46,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(136,4,47,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(137,4,48,'2025-05-16 13:44:51','2025-05-16 13:44:51'),(138,5,1,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(139,5,2,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(140,5,3,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(141,5,4,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(142,5,5,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(143,5,6,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(144,5,7,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(145,5,8,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(146,5,9,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(147,5,10,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(148,5,11,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(149,5,12,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(150,5,13,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(151,5,14,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(152,5,15,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(153,5,16,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(154,5,17,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(155,5,18,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(156,5,19,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(157,5,20,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(158,5,21,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(159,5,22,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(160,5,23,'2025-05-16 13:44:57','2025-05-16 13:44:57'),(161,5,24,'2025-05-16 13:44:57','2025-05-16 13:44:57');
/*!40000 ALTER TABLE `sw_ports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `switch_m_i_k_s`
--

DROP TABLE IF EXISTS `switch_m_i_k_s`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switch_m_i_k_s` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `total_ports` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switch_m_i_k_s`
--

LOCK TABLES `switch_m_i_k_s` WRITE;
/*!40000 ALTER TABLE `switch_m_i_k_s` DISABLE KEYS */;
INSERT INTO `switch_m_i_k_s` VALUES (1,'MKRT-01','Tegucigalpa',13,'2025-05-16 13:41:34','2025-05-16 13:41:34'),(2,'SW-A','Tegucigalpa',52,'2025-05-16 13:42:01','2025-05-16 13:42:01'),(3,'SW-B','Tegucigalpa',24,'2025-05-16 13:42:14','2025-05-16 13:42:14'),(4,'SW-C','Tegucigalpa',48,'2025-05-16 13:42:25','2025-05-16 13:42:25'),(5,'SW-D','Tegucigalpa',24,'2025-05-16 13:42:37','2025-05-16 13:42:37');
/*!40000 ALTER TABLE `switch_m_i_k_s` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@example.com',NULL,'$2y$12$BLQIkmsdIy6dIx2W1Wgx.urTabI23F38CE0C0mKE7cUWPxplq4k2e','flMA4HHQ9BoYsaZ0M3ZBRhK61zVbecV7QEh9JlMkEh8LSKOzDoQCNrzHphxq','2025-04-07 19:44:33','2025-04-07 19:44:33'),(2,'CELVYN LOZANO','clozano@megatk.com',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,'2025-04-14 22:42:23'),(3,'MYRTI SAUCEDA','msauceda@megatk.com',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,'2025-04-15 13:26:46'),(4,'ELISA RODRIGUEZ','1@3',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(5,'DARCY VASQUEZ','1@4',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(6,'KENIA CISNADO','1@5',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(7,'SARA FLORES','1@6',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(8,'DIANA COLINDRES','1@7',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(9,'DANIELA CHINCHILLA','1@8',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(10,'DAVID ZUNIGA','1@9',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(11,'ANTONIO CRUZ','1@10',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(12,'DARWIN RIVERA','1@11',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(13,'MARIO HERNANDEZ','1@12',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(14,'YAJAIRA ALVARADO','1@13',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(15,'MANUEL GARCIA','1@14',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(16,'DAVID MIDENCE','1@15',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(17,'LUIS DIAZ','ldiaz@megatk.com',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(18,'HARLING MARADIAGA','hmaradiaga@megatk.com',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,'2025-05-16 21:48:40'),(19,'KEVIN GODOY','1@18',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(20,'LAPTOP SOPORTE','1@19',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(21,'Noely Fuentes','1@20',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(22,'FANNY NUÑEZ','1@21',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(23,'Ventas Calle','1@22',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(24,'ESAU AGUILERA','1@23',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(25,'Jeovanny Romero','1@24',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(26,'ALEJANDRA SANCHEZ','1@25',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(27,'ROLANDO CACERES','1@26',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(28,'REINA SALGADO','1@27',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(29,'PLANTA TELEFONICA','1@28',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(30,'PENDIENTE IDENTIFICAR','1@29',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(31,'DENNIS GARCIA','1@30',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(32,'JENNIFER JOVEL','1@31',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(33,'LUIS MORAN','1@32',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(34,'VANESA MORAN','1@33',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(35,'MAURICIO CASTILLO','1@34',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(36,'MIGUEL MARQUEZ','1@35',NULL,'$12$cI3CbLhcGT.c8NUyLfp8ROftzKyKwR.ZJwsMuAVoyaIOCi6AUTWNu',NULL,NULL,NULL),(37,'rrrrrr rrrrrrrrrrrr','rrrrrrr@eeeeeeeeee',NULL,'$2y$12$D61uqNXDXZxiEfSRqYvF/Os.o8dNpBeNAhuhRXMArJuw6DRVfPUoC',NULL,'2025-04-15 22:08:52','2025-04-15 22:08:52'),(38,'EQUIPO ST','st@megatk.com',NULL,'$2y$12$2ql/eVAf5RmKlnU9QBX.6Oq6HQygwQEUPlU7F/0Ouq9D5ECfUoBhy',NULL,'2025-04-23 14:27:55','2025-04-23 14:30:40'),(39,'EQUIPO ADMINISTRACION','ADMIN@GMAIL.COM',NULL,'$2y$12$l.Unp9bITWOP7XQ1.3H3dOQZk5LekXDxboG1cjrenEtWOyktcs9j.',NULL,'2025-04-24 20:45:15','2025-04-24 20:45:15'),(40,'EQUIPO MEDITEK','MEDITEK@GMAIL.COM',NULL,'$2y$12$vaz8G3iwVUeM/g6u5TmthOadRLv/Bv8Gzv.jnEvHH4Rydo2MqguO.',NULL,'2025-04-24 20:46:04','2025-04-24 20:46:04'),(41,'CLAUDETH APL','C@MEGATK.COM',NULL,'$2y$12$3RAd8.lxOWgn1EXN5gX6h.5lY.g0fkhVvH3iQI5uJiNgLzNwt3g72',NULL,'2025-04-24 20:57:02','2025-04-24 20:57:02'),(42,'ELSY APL','qq@dd',NULL,'$2y$12$rUtFmo1UnWiDKlREckFxUu52hmGEidigjXA84F2x157DQyrAHb8/.',NULL,'2025-04-24 21:11:33','2025-04-24 21:11:33'),(43,'CAMARAS TGU','CAM@QWE.COM',NULL,'$2y$12$pi.oIEz4e3UMyV7sT8omdOOY/UCox.79p8NCHutybh2I4MhVzmYt.',NULL,'2025-04-29 15:18:40','2025-04-29 15:18:40');
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

-- Dump completed on 2025-05-20 22:09:23
