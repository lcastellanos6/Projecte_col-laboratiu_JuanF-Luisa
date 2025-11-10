-- MySQL dump 10.13  Distrib 8.4.6, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pruebas
-- ------------------------------------------------------
-- Server version	8.4.6

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
-- Table structure for table `clima`
--

DROP TABLE IF EXISTS `clima`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clima` (
  `id_clima` int NOT NULL AUTO_INCREMENT,
  `id_plantacio` int NOT NULL,
  `any_temp` int NOT NULL,
  `temperatura_mitjana` float DEFAULT NULL,
  `precipitacio_total` float DEFAULT NULL,
  `altres_factors` text,
  PRIMARY KEY (`id_clima`),
  KEY `id_plantacio` (`id_plantacio`),
  CONSTRAINT `clima_ibfk_1` FOREIGN KEY (`id_plantacio`) REFERENCES `plantacio` (`id_plantacio`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clima`
--

/*!40000 ALTER TABLE `clima` DISABLE KEYS */;
INSERT INTO `clima` VALUES (1,1,2025,18.5,450,'Invierno suave'),(2,2,2024,20.2,300,'Verano muy seco');
/*!40000 ALTER TABLE `clima` ENABLE KEYS */;

--
-- Table structure for table `data`
--

DROP TABLE IF EXISTS `data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data` (
  `id_data` int NOT NULL AUTO_INCREMENT,
  `data_inici` date NOT NULL,
  `data_fi` date DEFAULT NULL,
  PRIMARY KEY (`id_data`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data`
--

/*!40000 ALTER TABLE `data` DISABLE KEYS */;
INSERT INTO `data` VALUES (1,'2025-01-01','2025-12-31'),(2,'2024-01-01','2024-12-31');
/*!40000 ALTER TABLE `data` ENABLE KEYS */;

--
-- Table structure for table `especie`
--

DROP TABLE IF EXISTS `especie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `especie` (
  `id_especie` int NOT NULL AUTO_INCREMENT,
  `nom_cientific` varchar(255) NOT NULL,
  `nom_comu` varchar(255) NOT NULL,
  PRIMARY KEY (`id_especie`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especie`
--

/*!40000 ALTER TABLE `especie` DISABLE KEYS */;
INSERT INTO `especie` VALUES (1,'Prunus persica','Melocotonero'),(2,'Olea europaea','Olivo');
/*!40000 ALTER TABLE `especie` ENABLE KEYS */;

--
-- Table structure for table `fila`
--

DROP TABLE IF EXISTS `fila`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fila` (
  `id_fila` int NOT NULL AUTO_INCREMENT,
  `id_increment` int NOT NULL,
  `numero_fila` int NOT NULL,
  `geometria_fila` geometry NOT NULL,
  PRIMARY KEY (`id_fila`),
  UNIQUE KEY `id_increment` (`id_increment`,`numero_fila`),
  CONSTRAINT `fila_ibfk_1` FOREIGN KEY (`id_increment`) REFERENCES `plantacio` (`id_plantacio`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fila`
--

/*!40000 ALTER TABLE `fila` DISABLE KEYS */;
INSERT INTO `fila` VALUES (1,1,1,0xE610000001020000000200000000000000000000000000000000000000000000000000F03F0000000000000000),(2,1,2,0xE61000000102000000020000000000000000000000000000000000F03F000000000000F03F000000000000F03F);
/*!40000 ALTER TABLE `fila` ENABLE KEYS */;

--
-- Table structure for table `infra_parcela`
--

DROP TABLE IF EXISTS `infra_parcela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `infra_parcela` (
  `id_infra` int NOT NULL,
  `id_parcela` int NOT NULL,
  PRIMARY KEY (`id_infra`,`id_parcela`),
  KEY `id_parcela` (`id_parcela`),
  CONSTRAINT `infra_parcela_ibfk_1` FOREIGN KEY (`id_infra`) REFERENCES `infraestructura` (`id_infra`),
  CONSTRAINT `infra_parcela_ibfk_2` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_parcela`
--

/*!40000 ALTER TABLE `infra_parcela` DISABLE KEYS */;
INSERT INTO `infra_parcela` VALUES (1,1),(2,2);
/*!40000 ALTER TABLE `infra_parcela` ENABLE KEYS */;

--
-- Table structure for table `infraestructura`
--

DROP TABLE IF EXISTS `infraestructura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `infraestructura` (
  `id_infra` int NOT NULL AUTO_INCREMENT,
  `id_parcela` int DEFAULT NULL,
  `tipus` varchar(100) DEFAULT NULL,
  `descripcio` text,
  `geometria` geometry NOT NULL /*!80003 SRID 4326 */,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_infra`),
  KEY `id_parcela` (`id_parcela`),
  SPATIAL KEY `geometria` (`geometria`),
  CONSTRAINT `infraestructura_ibfk_1` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infraestructura`
--

/*!40000 ALTER TABLE `infraestructura` DISABLE KEYS */;
INSERT INTO `infraestructura` VALUES (1,1,'Riego','Sistema de riego por goteo',0xE61000000101000000000000000000E03F000000000000E03F,'<kml></kml>','riego.jpg','2025-09-30 07:58:01'),(2,2,'Camino','Acceso principal',0xE61000000101000000000000000000F83F000000000000F83F,'<kml></kml>','camino.jpg','2025-09-30 07:58:01');
/*!40000 ALTER TABLE `infraestructura` ENABLE KEYS */;

--
-- Table structure for table `parcela`
--

DROP TABLE IF EXISTS `parcela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parcela` (
  `id_parcela` int NOT NULL AUTO_INCREMENT,
  `ref_cadastral` varchar(50) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `superficie` decimal(10,2) DEFAULT NULL,
  `descripcio` text,
  `municipi` varchar(100) DEFAULT NULL,
  `geometria` geometry NOT NULL /*!80003 SRID 4326 */,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `edafo` text,
  `documentacio` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_parcela`),
  UNIQUE KEY `ref_cadastral` (`ref_cadastral`),
  SPATIAL KEY `geometria` (`geometria`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parcela`
--

/*!40000 ALTER TABLE `parcela` DISABLE KEYS */;
INSERT INTO `parcela` VALUES (1,'123ABC','Parcela Norte',15000.00,'Parcela de prueba','Valencia',0xE6100000010100000000000000000000000000000000000000,'<kml></kml>','foto1.jpg','Edafo info','Doc info','2025-09-30 07:57:46'),(2,'456DEF','Parcela Sur',12000.00,'Otra parcela','Castellón',0xE61000000101000000000000000000F03F000000000000F03F,'<kml></kml>','foto2.jpg','Edafo info','Doc info','2025-09-30 07:57:46');
/*!40000 ALTER TABLE `parcela` ENABLE KEYS */;

--
-- Table structure for table `plantacio`
--

DROP TABLE IF EXISTS `plantacio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plantacio` (
  `id_plantacio` int NOT NULL AUTO_INCREMENT,
  `id_sector` int NOT NULL,
  `id_varietat` int NOT NULL,
  `data_plantacio` date NOT NULL,
  `data_inici` date NOT NULL,
  `data_fi` date DEFAULT NULL,
  `marc_plantacio_files` decimal(5,2) DEFAULT NULL,
  `marc_plantacio_arbres` decimal(5,2) DEFAULT NULL,
  `num_arbres_total` int DEFAULT NULL,
  `origen_material` varchar(255) DEFAULT NULL,
  `certificacions` varchar(255) DEFAULT NULL,
  `sistema_formacio` varchar(100) DEFAULT NULL,
  `inversio_inicial` decimal(12,2) DEFAULT NULL,
  `entrada_produccio_prevista` year DEFAULT NULL,
  PRIMARY KEY (`id_plantacio`),
  KEY `id_sector` (`id_sector`),
  KEY `id_varietat` (`id_varietat`),
  CONSTRAINT `plantacio_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  CONSTRAINT `plantacio_ibfk_2` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plantacio`
--

/*!40000 ALTER TABLE `plantacio` DISABLE KEYS */;
INSERT INTO `plantacio` VALUES (1,1,1,'2025-02-01','2025-02-01',NULL,3.00,2.00,500,'Vivero Local','Certif ECO','Eje central',10000.00,2027),(2,2,2,'2024-03-01','2024-03-01',NULL,4.00,2.50,400,'Vivero Regional','Certif BIO','Vaso libre',8000.00,2026);
/*!40000 ALTER TABLE `plantacio` ENABLE KEYS */;

--
-- Table structure for table `registre`
--

DROP TABLE IF EXISTS `registre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registre` (
  `id_registre` int NOT NULL AUTO_INCREMENT,
  `id_varietat` int NOT NULL,
  `id_plantacio` int DEFAULT NULL,
  `data_plantacio` date NOT NULL,
  `data_arrencada` date DEFAULT NULL,
  `rendiment` decimal(10,2) DEFAULT NULL,
  `problemes_fitosanitaris` text,
  PRIMARY KEY (`id_registre`),
  KEY `id_varietat` (`id_varietat`),
  KEY `id_plantacio` (`id_plantacio`),
  CONSTRAINT `registre_ibfk_1` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`),
  CONSTRAINT `registre_ibfk_2` FOREIGN KEY (`id_plantacio`) REFERENCES `plantacio` (`id_plantacio`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registre`
--

/*!40000 ALTER TABLE `registre` DISABLE KEYS */;
INSERT INTO `registre` VALUES (1,1,1,'2025-02-01',NULL,2000.00,'Mildiu en primavera'),(2,2,2,'2024-03-01',NULL,1500.00,'Plaga de pulgón');
/*!40000 ALTER TABLE `registre` ENABLE KEYS */;

--
-- Table structure for table `sector`
--

DROP TABLE IF EXISTS `sector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector` (
  `id_sector` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `superficie` decimal(10,2) DEFAULT NULL,
  `geometria` geometry NOT NULL /*!80003 SRID 4326 */,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sector`),
  SPATIAL KEY `geometria` (`geometria`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector`
--

/*!40000 ALTER TABLE `sector` DISABLE KEYS */;
INSERT INTO `sector` VALUES (1,'Sector A',5000.00,0xE610000001030000000100000005000000000000000000000000000000000000000000000000000000000000000000F03F000000000000F03F000000000000F03F000000000000F03F000000000000000000000000000000000000000000000000,'<kml></kml>','sectorA.jpg','2025-09-30 07:57:48'),(2,'Sector B',4000.00,0xE610000001030000000100000005000000000000000000F03F000000000000F03F000000000000F03F0000000000000040000000000000004000000000000000400000000000000040000000000000F03F000000000000F03F000000000000F03F,'<kml></kml>','sectorB.jpg','2025-09-30 07:57:48');
/*!40000 ALTER TABLE `sector` ENABLE KEYS */;

--
-- Table structure for table `sector_parcela`
--

DROP TABLE IF EXISTS `sector_parcela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector_parcela` (
  `id_sector` int NOT NULL,
  `id_parcela` int NOT NULL,
  PRIMARY KEY (`id_sector`,`id_parcela`),
  KEY `idx_sp_parcela` (`id_parcela`),
  CONSTRAINT `sp_fk_parcela` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`),
  CONSTRAINT `sp_fk_sector` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector_parcela`
--

/*!40000 ALTER TABLE `sector_parcela` DISABLE KEYS */;
INSERT INTO `sector_parcela` VALUES (1,1),(2,2);
/*!40000 ALTER TABLE `sector_parcela` ENABLE KEYS */;

--
-- Table structure for table `sector_sol`
--

DROP TABLE IF EXISTS `sector_sol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector_sol` (
  `id_sector` int NOT NULL,
  `id_sol` int NOT NULL,
  PRIMARY KEY (`id_sector`,`id_sol`),
  KEY `id_sol` (`id_sol`),
  CONSTRAINT `sector_sol_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  CONSTRAINT `sector_sol_ibfk_2` FOREIGN KEY (`id_sol`) REFERENCES `sol` (`id_sol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector_sol`
--

/*!40000 ALTER TABLE `sector_sol` DISABLE KEYS */;
INSERT INTO `sector_sol` VALUES (1,1),(2,2);
/*!40000 ALTER TABLE `sector_sol` ENABLE KEYS */;

--
-- Table structure for table `sector_varietat`
--

DROP TABLE IF EXISTS `sector_varietat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector_varietat` (
  `id_sector` int NOT NULL,
  `id_varietat` int NOT NULL,
  `id_data` int NOT NULL,
  PRIMARY KEY (`id_sector`,`id_varietat`,`id_data`),
  KEY `id_varietat` (`id_varietat`),
  KEY `id_data` (`id_data`),
  CONSTRAINT `sector_varietat_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  CONSTRAINT `sector_varietat_ibfk_2` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`),
  CONSTRAINT `sector_varietat_ibfk_3` FOREIGN KEY (`id_data`) REFERENCES `data` (`id_data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector_varietat`
--

/*!40000 ALTER TABLE `sector_varietat` DISABLE KEYS */;
INSERT INTO `sector_varietat` VALUES (1,1,1),(2,2,2);
/*!40000 ALTER TABLE `sector_varietat` ENABLE KEYS */;

--
-- Table structure for table `seguiment`
--

DROP TABLE IF EXISTS `seguiment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguiment` (
  `id_seguiment` int NOT NULL AUTO_INCREMENT,
  `id_sector` int NOT NULL,
  `data_registre` date NOT NULL,
  `estat_fenologic` varchar(255) DEFAULT NULL,
  `creixement_vegetatiu` text,
  `incidencies_detectades` text,
  `intervencions_realitzades` text,
  `estimacio_actualizada_collita` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_seguiment`),
  KEY `id_sector` (`id_sector`),
  CONSTRAINT `seguiment_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguiment`
--

/*!40000 ALTER TABLE `seguiment` DISABLE KEYS */;
INSERT INTO `seguiment` VALUES (1,1,'2025-03-01','Floración','Brotes vigorosos','Mildiu incipiente','Tratamiento preventivo',1800.00),(2,2,'2024-06-10','Fructificación','Normal','Ataque de pulgón','Aplicación insecticida',1600.00);
/*!40000 ALTER TABLE `seguiment` ENABLE KEYS */;

--
-- Table structure for table `sol`
--

DROP TABLE IF EXISTS `sol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sol` (
  `id_sol` int NOT NULL AUTO_INCREMENT,
  `tipus` varchar(100) NOT NULL,
  `ph` decimal(4,2) DEFAULT NULL,
  `materia_organica` decimal(5,2) DEFAULT NULL,
  `observacions` text,
  PRIMARY KEY (`id_sol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sol`
--

/*!40000 ALTER TABLE `sol` DISABLE KEYS */;
INSERT INTO `sol` VALUES (1,'Arenoso',6.50,2.50,'Bien drenado'),(2,'Arcilloso',7.20,3.10,'Alta retención de agua');
/*!40000 ALTER TABLE `sol` ENABLE KEYS */;

--
-- Table structure for table `tractament`
--

DROP TABLE IF EXISTS `tractament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tractament` (
  `id_tractament` int NOT NULL AUTO_INCREMENT,
  `id_fila` int NOT NULL,
  `data` date NOT NULL,
  `tipus` enum('Fitosanitari','Fertilitzant','Altre') DEFAULT NULL,
  `producte` varchar(255) DEFAULT NULL,
  `dosi_ml` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_tractament`),
  KEY `id_fila` (`id_fila`),
  CONSTRAINT `tractament_ibfk_1` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tractament`
--

/*!40000 ALTER TABLE `tractament` DISABLE KEYS */;
INSERT INTO `tractament` VALUES (1,1,'2025-04-01','Fitosanitari','Fungicida X','200 ml'),(2,2,'2025-05-15','Fertilitzant','NPK 20-20-20','500 ml');
/*!40000 ALTER TABLE `tractament` ENABLE KEYS */;

--
-- Table structure for table `varietat`
--

DROP TABLE IF EXISTS `varietat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `varietat` (
  `id_varietat` int NOT NULL AUTO_INCREMENT,
  `id_especie` int NOT NULL,
  `nom_cientific` varchar(255) NOT NULL,
  `nom_comu` varchar(255) NOT NULL,
  `caracteristiques_agronomiques` text,
  `cicle_vegetatiu` text,
  `requisits_pollinitzacio` text,
  `productivitat_mitjana` decimal(10,2) DEFAULT NULL,
  `qualitats_organoleptiques` text,
  PRIMARY KEY (`id_varietat`),
  KEY `id_especie` (`id_especie`),
  CONSTRAINT `varietat_ibfk_1` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `varietat`
--

/*!40000 ALTER TABLE `varietat` DISABLE KEYS */;
INSERT INTO `varietat` VALUES (1,1,'Prunus persica var. platycarpa','Paraguayo','Resistente al frío','Caduco','Necesita polinizador',2500.50,'Dulce y jugoso'),(2,2,'Olea europaea var. arbequina','Arbequina','Alta productividad','Perennifolio','Autopolinizante',1800.00,'Aceite suave');
/*!40000 ALTER TABLE `varietat` ENABLE KEYS */;

--
-- Dumping routines for database 'pruebas'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-06 11:22:50
