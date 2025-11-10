-- MySQL dump 10.13  Distrib 8.4.6, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: projecte_sintesis
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `aplicacio`
--

DROP TABLE IF EXISTS `aplicacio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aplicacio` (
  `id_aplicacio` int(11) NOT NULL AUTO_INCREMENT,
  `id_pla` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `hora_inici` time DEFAULT NULL,
  `hora_fi` time DEFAULT NULL,
  `metode` enum('Fertirrigacio','Foliar','Sòl','Altres') DEFAULT NULL,
  `condicions_ambientals` text DEFAULT NULL,
  `id_operari` int(11) DEFAULT NULL,
  `id_equip` int(11) DEFAULT NULL,
  `observacions` text DEFAULT NULL,
  PRIMARY KEY (`id_aplicacio`),
  KEY `fk_ap_pla` (`id_pla`),
  KEY `fk_ap_oper` (`id_operari`),
  KEY `fk_ap_equip` (`id_equip`),
  CONSTRAINT `fk_ap_equip` FOREIGN KEY (`id_equip`) REFERENCES `equip` (`id_equip`),
  CONSTRAINT `fk_ap_oper` FOREIGN KEY (`id_operari`) REFERENCES `operari` (`id_operari`),
  CONSTRAINT `fk_ap_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aplicacio`
--

/*!40000 ALTER TABLE `aplicacio` DISABLE KEYS */;
INSERT INTO `aplicacio` VALUES (1,31,'2025-03-02','08:00:00','10:00:00','Foliar','Vent suau',1,1,'Sense incidències'),(2,32,'2025-05-12','07:30:00','09:00:00','Foliar','Matí fresc',2,2,'Plaga visible a les vores'),(3,33,'2025-02-02','09:00:00','11:00:00','Fertirrigacio','Ennuvolat',3,NULL,'Aplicació a capçalera'),(4,34,'2025-10-02','08:15:00','09:45:00','Foliar','Sec i temperat',4,3,'Buena cobertura'),(5,35,'2025-09-02','10:00:00','11:30:00','Sòl','Sòl humit',5,7,'Incorporado superficialmente'),(6,36,'2025-07-02','06:30:00','08:00:00','Foliar','Insolació alta',6,6,'Evitar les hores centrals'),(7,37,'2025-04-02','08:45:00','10:15:00','Fertirrigacio','Reg continu',7,NULL,'Ajust de cabal'),(8,38,'2025-05-03','07:00:00','08:30:00','Sòl','Postpluja',8,8,'Bon control de males herbes'),(9,39,'2025-03-16','09:30:00','10:30:00','Foliar','Floración inicial',9,9,'Aportació de calci'),(10,40,'2025-08-03','07:15:00','08:45:00','Foliar','Calor moderat',10,NULL,'Revisió general'),(11,41,'2025-02-03','09:45:00','11:45:00','Altres','Vent suau',11,11,'Sense incidències'),(12,42,'2025-03-04','06:15:00','08:15:00','Fertirrigacio','Cel serè',12,12,'Cobertura homogènia'),(13,43,'2025-04-05','07:30:00','09:30:00','Foliar','Vent suau',13,13,'Sense incidències'),(14,44,'2025-05-06','08:45:00','10:45:00','Sòl','Cel serè',14,14,'Cobertura homogènia'),(15,45,'2025-06-07','09:15:00','11:15:00','Altres','Vent suau',15,15,'Sense incidències'),(16,46,'2025-07-08','06:30:00','08:30:00','Fertirrigacio','Cel serè',16,16,'Cobertura homogènia'),(17,47,'2025-08-09','07:45:00','09:45:00','Foliar','Vent suau',17,17,'Sense incidències'),(18,48,'2025-09-01','08:15:00','10:15:00','Sòl','Cel serè',18,18,'Cobertura homogènia'),(19,49,'2025-10-02','09:30:00','11:30:00','Altres','Vent suau',19,19,'Sense incidències'),(20,50,'2025-11-03','06:45:00','08:45:00','Fertirrigacio','Cel serè',20,20,'Cobertura homogènia');
/*!40000 ALTER TABLE `aplicacio` ENABLE KEYS */;

--
-- Table structure for table `aplicacio_fila`
--

DROP TABLE IF EXISTS `aplicacio_fila`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aplicacio_fila` (
  `id_aplicacio` int(11) NOT NULL,
  `id_fila` int(11) NOT NULL,
  `estat` enum('Pendent','Fet','Parcial') DEFAULT 'Fet',
  `volum_caldo_l` decimal(10,2) DEFAULT NULL,
  `data_execucio` datetime DEFAULT NULL,
  `id_operari_execucio` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_aplicacio`,`id_fila`),
  KEY `fk_af_fila` (`id_fila`),
  KEY `fk_af_operari` (`id_operari_execucio`),
  CONSTRAINT `fk_af_ap` FOREIGN KEY (`id_aplicacio`) REFERENCES `aplicacio` (`id_aplicacio`) ON DELETE CASCADE,
  CONSTRAINT `fk_af_fila` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`),
  CONSTRAINT `fk_af_operari` FOREIGN KEY (`id_operari_execucio`) REFERENCES `operari` (`id_operari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aplicacio_fila`
--

/*!40000 ALTER TABLE `aplicacio_fila` DISABLE KEYS */;
INSERT INTO `aplicacio_fila` VALUES (1,1,'Fet',500.00,'2025-03-02 10:00:00',1),(2,2,'Fet',400.00,'2025-05-12 09:00:00',2),(3,1,'Fet',600.00,'2025-02-02 11:00:00',3),(4,2,'Fet',350.00,'2025-10-02 09:45:00',4),(5,1,'Fet',300.00,'2025-09-02 11:30:00',5),(6,2,'Parcial',280.00,'2025-07-02 08:00:00',6),(7,1,'Fet',450.00,'2025-04-02 10:15:00',7),(8,2,'Fet',320.00,'2025-05-03 08:30:00',8),(9,1,'Fet',250.00,'2025-03-16 10:30:00',9),(10,2,'Fet',300.00,'2025-08-03 08:45:00',10),(11,3,'Fet',340.00,'2025-02-03 11:45:00',11),(12,4,'Parcial',380.00,'2025-03-04 08:15:00',12),(13,5,'Fet',420.00,'2025-04-05 09:30:00',13),(14,6,'Fet',460.00,'2025-05-06 10:45:00',14),(15,7,'Parcial',300.00,'2025-06-07 11:15:00',15),(16,8,'Fet',340.00,'2025-07-08 08:30:00',16),(17,9,'Fet',380.00,'2025-08-09 09:45:00',17),(18,10,'Parcial',420.00,'2025-09-01 10:15:00',18),(19,11,'Fet',460.00,'2025-10-02 11:30:00',19),(20,12,'Fet',300.00,'2025-11-03 08:45:00',20);
/*!40000 ALTER TABLE `aplicacio_fila` ENABLE KEYS */;

--
-- Table structure for table `aplicacio_producte`
--

DROP TABLE IF EXISTS `aplicacio_producte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aplicacio_producte` (
  `id_aplicacio` int(11) NOT NULL,
  `id_producte` int(11) NOT NULL,
  `quantitat` decimal(10,3) NOT NULL,
  `unitat` enum('L','mL','kg','g') NOT NULL,
  `concentracio` varchar(50) DEFAULT NULL,
  `lot_referencia` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_aplicacio`,`id_producte`),
  KEY `fk_appr_pr` (`id_producte`),
  CONSTRAINT `fk_appr_ap` FOREIGN KEY (`id_aplicacio`) REFERENCES `aplicacio` (`id_aplicacio`) ON DELETE CASCADE,
  CONSTRAINT `fk_appr_pr` FOREIGN KEY (`id_producte`) REFERENCES `producte` (`id_producte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aplicacio_producte`
--

/*!40000 ALTER TABLE `aplicacio_producte` DISABLE KEYS */;
INSERT INTO `aplicacio_producte` VALUES (1,1,20.000,'L','2%','L001'),(2,2,12.000,'L','1%','L002'),(3,3,25.000,'kg','—','L003'),(4,8,10.000,'kg','—','L008'),(5,7,5.000,'L','—','L007'),(6,6,3.000,'L','—','L006'),(7,9,6.000,'L','—','L009'),(8,4,8.000,'L','—','L004'),(9,5,10.000,'kg','—','L005'),(10,10,4.000,'L','—','L010'),(11,11,6.000,'L','—','L011'),(12,12,7.000,'kg','—','L012'),(13,13,8.000,'L','—','L013'),(14,14,9.000,'kg','—','L014'),(15,15,5.000,'L','—','L015'),(16,16,6.000,'kg','—','L016'),(17,17,7.000,'L','—','L017'),(18,18,8.000,'kg','—','L018'),(19,19,9.000,'L','—','L019'),(20,20,5.000,'kg','—','L020');
/*!40000 ALTER TABLE `aplicacio_producte` ENABLE KEYS */;

--
-- Table structure for table `clima`
--

DROP TABLE IF EXISTS `clima`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clima` (
  `id_clima` int(11) NOT NULL AUTO_INCREMENT,
  `id_plantacio` int(11) NOT NULL,
  `any_temp` int(11) NOT NULL,
  `temperatura_mitjana` float DEFAULT NULL,
  `precipitacio_total` float DEFAULT NULL,
  `altres_factors` text DEFAULT NULL,
  PRIMARY KEY (`id_clima`),
  KEY `id_plantacio` (`id_plantacio`),
  CONSTRAINT `clima_ibfk_1` FOREIGN KEY (`id_plantacio`) REFERENCES `plantacio` (`id_plantacio`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clima`
--

/*!40000 ALTER TABLE `clima` DISABLE KEYS */;
INSERT INTO `clima` VALUES (1,1,2025,18.5,450,'Hivern suau'),(2,2,2024,20.2,300,'Estiu molt sec'),(3,3,2025,20.5,490,'Registre automàtic d’estació'),(4,4,2024,21.5,520,'Registre automàtic d’estació'),(5,5,2023,17.5,550,'Registre automàtic d’estació'),(6,6,2025,18.5,400,'Registre automàtic d’estació'),(7,7,2024,19.5,430,'Registre automàtic d’estació'),(8,8,2023,20.5,460,'Registre automàtic d’estació'),(9,9,2025,21.5,490,'Registre automàtic d’estació'),(10,10,2024,17.5,520,'Registre automàtic d’estació'),(11,11,2023,18.5,550,'Registre automàtic d’estació'),(12,12,2025,19.5,400,'Registre automàtic d’estació');
/*!40000 ALTER TABLE `clima` ENABLE KEYS */;

--
-- Table structure for table `collita`
--

DROP TABLE IF EXISTS `collita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collita` (
  `collita_id` int(11) NOT NULL AUTO_INCREMENT,
  `data_inici` datetime NOT NULL,
  `data_fi` datetime DEFAULT NULL,
  `plantacio_id` int(11) NOT NULL,
  `quantitat_total` decimal(10,2) DEFAULT NULL,
  `unitat` enum('kg','caixa','bin') NOT NULL DEFAULT 'kg',
  `condicions_ambientals` text DEFAULT NULL,
  `id_estat` int(11) DEFAULT NULL,
  `maduresa` varchar(100) DEFAULT NULL,
  `incidencies` text DEFAULT NULL,
  `id_operari` int(11) DEFAULT NULL,
  `id_equip` int(11) DEFAULT NULL,
  PRIMARY KEY (`collita_id`),
  KEY `fk_co_plantacio` (`plantacio_id`),
  KEY `fk_co_estat` (`id_estat`),
  KEY `fk_co_operari` (`id_operari`),
  KEY `fk_co_equip` (`id_equip`),
  CONSTRAINT `fk_co_equip` FOREIGN KEY (`id_equip`) REFERENCES `equip` (`id_equip`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_co_estat` FOREIGN KEY (`id_estat`) REFERENCES `estat_fenologic` (`id_estat`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_co_operari` FOREIGN KEY (`id_operari`) REFERENCES `operari` (`id_operari`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_co_plantacio` FOREIGN KEY (`plantacio_id`) REFERENCES `plantacio` (`id_plantacio`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collita`
--

/*!40000 ALTER TABLE `collita` DISABLE KEYS */;
INSERT INTO `collita` VALUES (1,'2025-06-05 07:30:00','2025-06-05 12:00:00',1,1250.00,'kg','Cel serè 20–24 ºC',8,'Brix 12.5','Cap incidència',1,12),(2,'2025-07-12 07:15:00','2025-07-12 11:30:00',3,980.00,'kg','Lleu vent; humitat 60%',8,'Brix 13.0','Zones ombrívoles més lentes',2,9),(3,'2025-08-20 06:45:00','2025-08-20 12:15:00',4,1520.00,'kg','Sec i temperat',8,'Brix 12.8','Sense',3,12),(4,'2025-09-03 07:00:00','2025-09-03 10:45:00',5,1100.00,'kg','Postpluja, sòl mullat',8,'Brix 12.2','Humitat en zones baixes',4,9);
/*!40000 ALTER TABLE `collita` ENABLE KEYS */;

--
-- Table structure for table `collita_equip`
--

DROP TABLE IF EXISTS `collita_equip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collita_equip` (
  `collita_id` int(11) NOT NULL,
  `id_equip` int(11) NOT NULL,
  PRIMARY KEY (`collita_id`,`id_equip`),
  KEY `fk_ce_eq` (`id_equip`),
  CONSTRAINT `fk_ce_co` FOREIGN KEY (`collita_id`) REFERENCES `collita` (`collita_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ce_eq` FOREIGN KEY (`id_equip`) REFERENCES `equip` (`id_equip`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collita_equip`
--

/*!40000 ALTER TABLE `collita_equip` DISABLE KEYS */;
INSERT INTO `collita_equip` VALUES (1,9),(1,12),(2,9),(3,12),(4,9);
/*!40000 ALTER TABLE `collita_equip` ENABLE KEYS */;

--
-- Table structure for table `collita_fila`
--

DROP TABLE IF EXISTS `collita_fila`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collita_fila` (
  `collita_id` int(11) NOT NULL,
  `id_fila` int(11) NOT NULL,
  PRIMARY KEY (`collita_id`,`id_fila`),
  KEY `fk_cf_fila` (`id_fila`),
  CONSTRAINT `fk_cf_co` FOREIGN KEY (`collita_id`) REFERENCES `collita` (`collita_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cf_fila` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collita_fila`
--

/*!40000 ALTER TABLE `collita_fila` DISABLE KEYS */;
INSERT INTO `collita_fila` VALUES (1,1),(1,2),(2,3),(3,4),(4,5);
/*!40000 ALTER TABLE `collita_fila` ENABLE KEYS */;

--
-- Table structure for table `collita_operari`
--

DROP TABLE IF EXISTS `collita_operari`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collita_operari` (
  `collita_id` int(11) NOT NULL,
  `id_operari` int(11) NOT NULL,
  `rol` enum('Cap_equip','Recol·lector','Conductor','Altres') DEFAULT 'Recol·lector',
  PRIMARY KEY (`collita_id`,`id_operari`),
  KEY `fk_coo_op` (`id_operari`),
  CONSTRAINT `fk_coo_co` FOREIGN KEY (`collita_id`) REFERENCES `collita` (`collita_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_coo_op` FOREIGN KEY (`id_operari`) REFERENCES `operari` (`id_operari`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collita_operari`
--

/*!40000 ALTER TABLE `collita_operari` DISABLE KEYS */;
INSERT INTO `collita_operari` VALUES (1,1,'Cap_equip'),(1,5,'Recol·lector'),(1,6,'Recol·lector'),(2,2,'Cap_equip'),(2,7,'Recol·lector'),(3,3,'Cap_equip'),(3,8,'Recol·lector'),(4,4,'Cap_equip'),(4,9,'Recol·lector');
/*!40000 ALTER TABLE `collita_operari` ENABLE KEYS */;

--
-- Table structure for table `control_qualitat`
--

DROP TABLE IF EXISTS `control_qualitat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `control_qualitat` (
  `control_id` int(11) NOT NULL AUTO_INCREMENT,
  `lot_id` int(11) NOT NULL,
  `data_control` date NOT NULL,
  `calibre` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `fermesa` varchar(50) DEFAULT NULL,
  `defectes` text DEFAULT NULL,
  `sabor` varchar(50) DEFAULT NULL,
  `aroma` varchar(50) DEFAULT NULL,
  `observacions` text DEFAULT NULL,
  `qualificacio_final` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`control_id`),
  KEY `fk_cq_lot` (`lot_id`),
  CONSTRAINT `fk_cq_lot` FOREIGN KEY (`lot_id`) REFERENCES `lot_produccio` (`lot_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `control_qualitat`
--

/*!40000 ALTER TABLE `control_qualitat` DISABLE KEYS */;
INSERT INTO `control_qualitat` VALUES (1,1,'2025-06-05','70–75 mm','Color òptim','Alta','Sense defectes','Dolç','Aromàtic','Mostreig sortida camp','A'),(2,2,'2025-06-05','65–70 mm','Lleu verd','Mitja','Picades lleus','Dolç','Mitjà','Classificat com 1a','B'),(3,3,'2025-07-12','Mixt','Homogeni','Mitja','Cap','Neutre','Suau','Apte Horeca','B'),(4,4,'2025-08-20','80–85 mm','Intens','Alta','Cap','Dolç','Alt','Apte exportació','A'),(5,5,'2025-09-03','Mixt','Bo','Mitja','Rebuig < 3%','Dolç','Mitjà','Conservar en fred','B');
/*!40000 ALTER TABLE `control_qualitat` ENABLE KEYS */;

--
-- Table structure for table `data`
--

DROP TABLE IF EXISTS `data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data` (
  `id_data` int(11) NOT NULL AUTO_INCREMENT,
  `data_inici` date NOT NULL,
  `data_fi` date DEFAULT NULL,
  PRIMARY KEY (`id_data`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data`
--

/*!40000 ALTER TABLE `data` DISABLE KEYS */;
INSERT INTO `data` VALUES (1,'2025-01-01','2025-12-31'),(2,'2024-01-01','2024-12-31'),(3,'2023-01-01','2023-12-31'),(4,'2022-01-01','2022-12-31'),(5,'2021-01-01','2021-12-31'),(6,'2020-01-01','2020-12-31'),(7,'2019-01-01','2019-12-31'),(8,'2018-01-01','2018-12-31'),(9,'2017-01-01','2017-12-31'),(10,'2016-01-01','2016-12-31'),(11,'2015-01-01','2015-12-31'),(12,'2014-01-01','2014-12-31');
/*!40000 ALTER TABLE `data` ENABLE KEYS */;

--
-- Table structure for table `desti_client`
--

DROP TABLE IF EXISTS `desti_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `desti_client` (
  `id_client` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `cognoms` varchar(150) DEFAULT NULL,
  `nif_cif` varchar(20) DEFAULT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `adreca` varchar(255) DEFAULT NULL,
  `observacions` text DEFAULT NULL,
  PRIMARY KEY (`id_client`),
  UNIQUE KEY `nif_cif` (`nif_cif`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `desti_client`
--

/*!40000 ALTER TABLE `desti_client` DISABLE KEYS */;
INSERT INTO `desti_client` VALUES (1,'Cooperativa La Ribera',NULL,'B12345678','963000111','compres@laribera.coop','C/ Major 10, Alzira','Client principal'),(2,'Frutas Mediterráneo SL',NULL,'B87654321','961234567','compras@frutasm.es','Pol. Ind. Levante, Castelló','Exportació i nacional'),(3,'Horeca Fresh',NULL,'B11122233','960112233','pedidos@horecafresh.es','Av. Servei 22, València','Canal restauració'),(4,'Export Citrus Ltd',NULL,'GB123456789','+44 20 1111 2222','buy@exportcitrus.co.uk','45 Fruit Rd, London','Export UK');
/*!40000 ALTER TABLE `desti_client` ENABLE KEYS */;

--
-- Table structure for table `equip`
--

DROP TABLE IF EXISTS `equip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equip` (
  `id_equip` int(11) NOT NULL AUTO_INCREMENT,
  `tipus` varchar(100) NOT NULL,
  `descripcio` text DEFAULT NULL,
  PRIMARY KEY (`id_equip`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equip`
--

/*!40000 ALTER TABLE `equip` DISABLE KEYS */;
INSERT INTO `equip` VALUES (1,'Polvoritzador','Equip de motxilla manual'),(2,'Tractor','Tractor John Deere 5050E'),(3,'Atomitzador','Atomitzador de arrastre 1000L'),(4,'Bomba elèctrica','Bomba de reg portàtil'),(5,'Sistema de degoteig','Instal·lació de reg per degoteig'),(6,'Desbrossadora','Desbrossadora Stihl FS 260'),(7,'Sembradora','Sembradora mecánica'),(8,'Cisterna','Cisterna d’aigua 3000L'),(9,'Carretó','Carretó manual de transporte'),(10,'Equip de protecció','EPI per a aplicació de productes'),(11,'Podadora elèctrica','Tisora elèctrica de poda'),(12,'Remolc agrícola','Remolc basculant 3T'),(13,'Estació meteorològica','Registre meteo automàtic'),(14,'Dron agrícola','Dron per a monitoratge NDVI'),(15,'GPS agrícola','Guia paral·lela en tractor'),(16,'Motocultor','Motocultor gasolina 7HP'),(17,'Hidronetejadora','Neteja d’equips'),(18,'Mesurador pH','Mesurador pH/CE portátil'),(19,'Cuba 2000L','Dipòsit mòbil 2000L'),(20,'Trituradora de restes','Trituradora de martells');
/*!40000 ALTER TABLE `equip` ENABLE KEYS */;

--
-- Table structure for table `especie`
--

DROP TABLE IF EXISTS `especie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `especie` (
  `id_especie` int(11) NOT NULL AUTO_INCREMENT,
  `nom_cientific` varchar(255) NOT NULL,
  `nom_comu` varchar(255) NOT NULL,
  PRIMARY KEY (`id_especie`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especie`
--

/*!40000 ALTER TABLE `especie` DISABLE KEYS */;
INSERT INTO `especie` VALUES (1,'Prunus persica','Presseguer'),(2,'Olea europaea','Olivera'),(3,'Vitis vinifera','Vinya'),(4,'Citrus sinensis','Taronger dolç'),(5,'Citrus limon','Llimoner'),(6,'Pyrus communis','Perera'),(7,'Malus domestica','Pomer'),(8,'Prunus dulcis','Ametller'),(9,'Prunus armeniaca','Albercoquer'),(10,'Prunus avium','Cirerer'),(11,'Ficus carica','Figuera'),(12,'Punica granatum','Magraner');
/*!40000 ALTER TABLE `especie` ENABLE KEYS */;

--
-- Table structure for table `estat_fenologic`
--

DROP TABLE IF EXISTS `estat_fenologic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estat_fenologic` (
  `id_estat` int(11) NOT NULL AUTO_INCREMENT,
  `codi` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id_estat`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estat_fenologic`
--

/*!40000 ALTER TABLE `estat_fenologic` DISABLE KEYS */;
INSERT INTO `estat_fenologic` VALUES (1,'E1','Repòs hivernal'),(2,'E2','Brotació'),(3,'E3','Floració'),(4,'E4','Quallat'),(5,'E5','Fructificació'),(6,'E6','Engreix'),(7,'E7','Enverol'),(8,'E8','Maduració'),(9,'E9','Postcollita'),(10,'E10','Aturada vegetativa'),(11,'E11','Fase 11'),(12,'E12','Fase 12'),(13,'E13','Fase 13'),(14,'E14','Fase 14'),(15,'E15','Fase 15'),(16,'E16','Fase 16'),(17,'E17','Fase 17'),(18,'E18','Fase 18'),(19,'E19','Fase 19'),(20,'E20','Fase 20');
/*!40000 ALTER TABLE `estat_fenologic` ENABLE KEYS */;

--
-- Table structure for table `fila`
--

DROP TABLE IF EXISTS `fila`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fila` (
  `id_fila` int(11) NOT NULL AUTO_INCREMENT,
  `id_increment` int(11) NOT NULL,
  `numero_fila` int(11) NOT NULL,
  `geometria_fila` geometry NOT NULL,
  PRIMARY KEY (`id_fila`),
  UNIQUE KEY `id_increment` (`id_increment`,`numero_fila`),
  CONSTRAINT `fila_ibfk_1` FOREIGN KEY (`id_increment`) REFERENCES `plantacio` (`id_plantacio`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fila`
--

/*!40000 ALTER TABLE `fila` DISABLE KEYS */;
INSERT INTO `fila` VALUES (1,1,1,0xE610000001020000000200000000000000000000000000000000000000000000000000F03F0000000000000000),(2,1,2,0xE61000000102000000020000000000000000000000000000000000F03F000000000000F03F000000000000F03F),(3,3,1,0xE6100000010200000002000000295C8FC2F5B84340C2F5285C8FC2E1BF295C8FC2F5B84340999999999999E1BF),(4,4,1,0xE61000000102000000020000007B14AE47E1BA434048E17A14AE47E1BF7B14AE47E1BA43401F85EB51B81EE1BF),(5,5,1,0xE6100000010200000002000000CDCCCCCCCCBC4340CDCCCCCCCCCCE0BFCDCCCCCCCCBC4340A4703D0AD7A3E0BF),(6,6,1,0xE61000000102000000020000001F85EB51B8BE434052B81E85EB51E0BF1F85EB51B8BE4340295C8FC2F528E0BF),(7,7,1,0xE6100000010200000002000000703D0AD7A3C04340AE47E17A14AEDFBF703D0AD7A3C043405C8FC2F5285CDFBF),(8,8,1,0xE6100000010200000002000000C2F5285C8FC24340B81E85EB51B8DEBFC2F5285C8FC24340666666666666DEBF),(9,9,1,0xE610000001020000000200000014AE47E17AC44340C2F5285C8FC2DDBF14AE47E17AC44340703D0AD7A370DDBF),(10,10,1,0xE61000000102000000020000006666666666C64340CCCCCCCCCCCCDCBF6666666666C643407A14AE47E17ADCBF),(11,11,1,0xE6100000010200000002000000B81E85EB51C84340D7A3703D0AD7DBBFB81E85EB51C8434085EB51B81E85DBBF),(12,12,1,0xE61000000102000000020000000AD7A3703DCA4340E17A14AE47E1DABF0AD7A3703DCA43408FC2F5285C8FDABF);
/*!40000 ALTER TABLE `fila` ENABLE KEYS */;

--
-- Table structure for table `infra_parcela`
--

DROP TABLE IF EXISTS `infra_parcela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `infra_parcela` (
  `id_infra` int(11) NOT NULL,
  `id_parcela` int(11) NOT NULL,
  PRIMARY KEY (`id_infra`,`id_parcela`),
  KEY `id_parcela` (`id_parcela`),
  CONSTRAINT `infra_parcela_ibfk_1` FOREIGN KEY (`id_infra`) REFERENCES `infraestructura` (`id_infra`),
  CONSTRAINT `infra_parcela_ibfk_2` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_parcela`
--

/*!40000 ALTER TABLE `infra_parcela` DISABLE KEYS */;
INSERT INTO `infra_parcela` VALUES (1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(12,12);
/*!40000 ALTER TABLE `infra_parcela` ENABLE KEYS */;

--
-- Table structure for table `infraestructura`
--

DROP TABLE IF EXISTS `infraestructura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `infraestructura` (
  `id_infra` int(11) NOT NULL AUTO_INCREMENT,
  `id_parcela` int(11) DEFAULT NULL,
  `tipus` varchar(100) DEFAULT NULL,
  `descripcio` text DEFAULT NULL,
  `geometria` geometry NOT NULL,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_infra`),
  KEY `id_parcela` (`id_parcela`),
  SPATIAL KEY `geometria` (`geometria`),
  CONSTRAINT `infraestructura_ibfk_1` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infraestructura`
--

/*!40000 ALTER TABLE `infraestructura` DISABLE KEYS */;
INSERT INTO `infraestructura` VALUES (1,1,'Reg','Sistema de reg per degoteig',0xE61000000101000000000000000000E03F000000000000E03F,'<kml></kml>','riego.jpg','2025-09-30 07:58:01'),(2,2,'Camí','Accés principal',0xE61000000101000000000000000000F83F000000000000F83F,'<kml></kml>','camino.jpg','2025-09-30 07:58:01'),(3,3,'Pou','Infraestructura Pou 3',0xE61000000101000000E27A14AE47C143405D8FC2F5285CDFBF,'<kml></kml>','infra3.jpg','2025-10-14 08:53:14'),(4,4,'Caseta','Infraestructura Caseta 4',0xE61000000101000000A4703D0AD7C3434015AE47E17A14DEBF,'<kml></kml>','infra4.jpg','2025-10-14 08:53:14'),(5,5,'Bassa','Infraestructura Bassa 5',0xE610000001010000006766666666C64340CECCCCCCCCCCDCBF,'<kml></kml>','infra5.jpg','2025-10-14 08:53:14'),(6,6,'Tanca','Infraestructura Tanca 6',0xE61000000101000000295C8FC2F5C8434086EB51B81E85DBBF,'<kml></kml>','infra6.jpg','2025-10-14 08:53:14'),(7,7,'Camí','Infraestructura Camí 7',0xE61000000101000000EC51B81E85CB43403E0AD7A3703DDABF,'<kml></kml>','infra7.jpg','2025-10-14 08:53:14'),(8,8,'Reg','Infraestructura Reg 8',0xE61000000101000000AE47E17A14CE4340F6285C8FC2F5D8BF,'<kml></kml>','infra8.jpg','2025-10-14 08:53:14'),(9,9,'Aljibe','Infraestructura Aljub 9',0xE61000000101000000713D0AD7A3D04340AF47E17A14AED7BF,'<kml></kml>','infra9.jpg','2025-10-14 08:53:14'),(10,10,'Punt de càrrega','Infraestructura Punt de càrrega 10',0xE610000001010000003433333333D34340676666666666D6BF,'<kml></kml>','infra10.jpg','2025-10-14 08:53:14'),(11,11,'Cobert d\'eines','Infraestructura Cobert d’eines 11',0xE61000000101000000F6285C8FC2D543402085EB51B81ED5BF,'<kml></kml>','infra11.jpg','2025-10-14 08:53:14'),(12,12,'Estació meteorològica','Infraestructura Estació meteorològica 12',0xE61000000101000000B91E85EB51D84340D8A3703D0AD7D3BF,'<kml></kml>','infra12.jpg','2025-10-14 08:53:14');
/*!40000 ALTER TABLE `infraestructura` ENABLE KEYS */;

--
-- Table structure for table `lot_produccio`
--

DROP TABLE IF EXISTS `lot_produccio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lot_produccio` (
  `lot_id` int(11) NOT NULL AUTO_INCREMENT,
  `collita_id` int(11) NOT NULL,
  `codi_lot` varchar(100) NOT NULL,
  `data_creacio` date NOT NULL,
  `quantitat` decimal(10,2) NOT NULL,
  `unitat` enum('kg','caixa','bin') NOT NULL DEFAULT 'kg',
  `qualitat` varchar(50) DEFAULT NULL,
  `estat` enum('Emmagatzemat','En transport','Venut') NOT NULL DEFAULT 'Emmagatzemat',
  `id_client` int(11) DEFAULT NULL,
  `qr_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`lot_id`),
  UNIQUE KEY `codi_lot` (`codi_lot`),
  KEY `fk_lp_co` (`collita_id`),
  KEY `fk_lp_client` (`id_client`),
  CONSTRAINT `fk_lp_client` FOREIGN KEY (`id_client`) REFERENCES `desti_client` (`id_client`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_lp_co` FOREIGN KEY (`collita_id`) REFERENCES `collita` (`collita_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_produccio`
--

/*!40000 ALTER TABLE `lot_produccio` DISABLE KEYS */;
INSERT INTO `lot_produccio` VALUES (1,1,'COL-2025-0001-A','2025-06-05',800.00,'kg','Extra','Emmagatzemat',1,'https://trace/lot/COL-2025-0001-A'),(2,1,'COL-2025-0001-B','2025-06-05',450.00,'kg','I','Emmagatzemat',2,'https://trace/lot/COL-2025-0001-B'),(3,2,'COL-2025-0002-A','2025-07-12',980.00,'kg','I','En transport',3,'https://trace/lot/COL-2025-0002-A'),(4,3,'COL-2025-0003-A','2025-08-20',1520.00,'kg','Extra','Emmagatzemat',4,'https://trace/lot/COL-2025-0003-A'),(5,4,'COL-2025-0004-A','2025-09-03',1100.00,'kg','I','Emmagatzemat',1,'https://trace/lot/COL-2025-0004-A');
/*!40000 ALTER TABLE `lot_produccio` ENABLE KEYS */;

--
-- Table structure for table `magatzem`
--

DROP TABLE IF EXISTS `magatzem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magatzem` (
  `id_magatzem` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `ubicacio` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_magatzem`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `magatzem`
--

/*!40000 ALTER TABLE `magatzem` DISABLE KEYS */;
INSERT INTO `magatzem` VALUES (1,'Magatzem central','València'),(2,'Dipòsit nord','Castelló'),(3,'Magatzem sud','Alacant'),(4,'Dipòsit est','Requena'),(5,'Cambra freda','València'),(6,'Dipòsit de líquids','Alzira'),(7,'Magatzem de sòlids','Sueca'),(8,'Dipòsit de fertilitzants','Xàtiva'),(9,'Magatzem antic','Gandia'),(10,'Dipòsit auxiliar','Onda'),(11,'Magatzem oest','Utiel'),(12,'Dipòsit sud 2','Elx'),(13,'Magatzem Montes','Morella'),(14,'Dipòsit Ribera','Cullera'),(15,'Cambra frigorífica','Sagunt'),(16,'Nau auxiliar','Ontinyent'),(17,'Dipòsit de químics','Paterna'),(18,'Magatzem de recanvis','Torrent'),(19,'Dipòsit fitosanitaris','Alfafar'),(20,'Base de camp','Bunyol');
/*!40000 ALTER TABLE `magatzem` ENABLE KEYS */;

--
-- Table structure for table `moviment_estoc`
--

DROP TABLE IF EXISTS `moviment_estoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `moviment_estoc` (
  `id_mov` int(11) NOT NULL AUTO_INCREMENT,
  `id_lot` int(11) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp(),
  `quantitat` decimal(12,3) NOT NULL,
  `motiu` enum('Compra','Ajust','Aplicacio') NOT NULL,
  `id_aplicacio` int(11) DEFAULT NULL,
  `observacions` text DEFAULT NULL,
  PRIMARY KEY (`id_mov`),
  KEY `fk_me_lot` (`id_lot`),
  KEY `fk_me_ap` (`id_aplicacio`),
  CONSTRAINT `fk_me_ap` FOREIGN KEY (`id_aplicacio`) REFERENCES `aplicacio` (`id_aplicacio`),
  CONSTRAINT `fk_me_lot` FOREIGN KEY (`id_lot`) REFERENCES `producte_lot` (`id_lot`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moviment_estoc`
--

/*!40000 ALTER TABLE `moviment_estoc` DISABLE KEYS */;
INSERT INTO `moviment_estoc` VALUES (1,1,'2025-01-01 10:00:00',50.000,'Compra',NULL,'Alta d’estoc'),(2,2,'2025-02-01 10:00:00',40.000,'Compra',NULL,'Alta d’estoc'),(3,3,'2025-02-01 11:10:00',100.000,'Compra',NULL,'Alta d’estoc'),(4,4,'2025-03-20 10:00:00',75.000,'Compra',NULL,'Alta d’estoc'),(5,5,'2025-03-15 10:40:00',90.000,'Compra',NULL,'Alta d’estoc'),(6,1,'2025-03-02 11:00:00',-20.000,'Aplicacio',1,'Usat a l’aplicació 1'),(7,2,'2025-05-12 09:10:00',-12.000,'Aplicacio',2,'Usat a l’aplicació 2'),(8,3,'2025-02-02 11:10:00',-25.000,'Aplicacio',3,'Usat a l’aplicació 3'),(9,8,'2025-10-02 09:50:00',-10.000,'Aplicacio',4,'Usat a l’aplicació 4'),(10,5,'2025-03-16 10:40:00',-10.000,'Aplicacio',9,'Usat a l’aplicació 9'),(11,11,'2025-01-11 00:00:00',70.000,'Compra',NULL,'Alta d’estoc'),(12,12,'2025-03-04 00:00:00',-5.000,'Aplicacio',12,'Consum a l’aplicació 12'),(13,13,'2025-01-13 00:00:00',60.000,'Compra',NULL,'Alta d’estoc'),(14,14,'2025-05-06 00:00:00',-9.000,'Aplicacio',14,'Consum a l’aplicació 14'),(15,15,'2025-01-15 00:00:00',50.000,'Compra',NULL,'Alta d’estoc'),(16,16,'2025-07-08 00:00:00',-5.000,'Aplicacio',16,'Consum a l’aplicació 16'),(17,17,'2025-01-17 00:00:00',70.000,'Compra',NULL,'Alta d’estoc'),(18,18,'2025-09-01 00:00:00',-9.000,'Aplicacio',18,'Consum a l’aplicació 18'),(19,19,'2025-01-19 00:00:00',60.000,'Compra',NULL,'Alta d’estoc'),(20,20,'2025-11-03 00:00:00',-5.000,'Aplicacio',20,'Consum a l’aplicació 20'),(21,7,'2025-09-02 00:00:00',-5.000,'Aplicacio',5,'Auto-ajuste L app 5'),(22,6,'2025-07-02 00:00:00',-3.000,'Aplicacio',6,'Auto-ajuste L app 6'),(23,9,'2025-04-02 00:00:00',-6.000,'Aplicacio',7,'Auto-ajuste L app 7'),(24,4,'2025-05-03 00:00:00',-8.000,'Aplicacio',8,'Auto-ajuste L app 8'),(25,10,'2025-08-03 00:00:00',-4.000,'Aplicacio',10,'Auto-ajuste L app 10'),(26,11,'2025-02-03 00:00:00',-6.000,'Aplicacio',11,'Auto-ajuste L app 11'),(27,13,'2025-04-05 00:00:00',-8.000,'Aplicacio',13,'Auto-ajuste L app 13'),(28,15,'2025-06-07 00:00:00',-5.000,'Aplicacio',15,'Auto-ajuste L app 15'),(29,17,'2025-08-09 00:00:00',-7.000,'Aplicacio',17,'Auto-ajuste L app 17'),(30,19,'2025-10-02 00:00:00',-9.000,'Aplicacio',19,'Auto-ajuste L app 19'),(36,6,'2025-04-10 00:00:00',33.000,'Compra',NULL,'Compra generada per coherència històrica'),(37,7,'2025-05-01 00:00:00',125.000,'Compra',NULL,'Compra generada per coherència històrica'),(38,8,'2025-05-15 00:00:00',65.000,'Compra',NULL,'Compra generada per coherència històrica'),(39,9,'2025-04-01 00:00:00',76.000,'Compra',NULL,'Compra generada per coherència històrica'),(40,10,'2025-06-10 00:00:00',49.000,'Compra',NULL,'Compra generada per coherència històrica'),(41,12,'2025-02-01 00:00:00',105.000,'Compra',NULL,'Compra generada per coherència històrica'),(42,14,'2025-04-01 00:00:00',149.000,'Compra',NULL,'Compra generada per coherència històrica'),(43,16,'2025-06-01 00:00:00',85.000,'Compra',NULL,'Compra generada per coherència històrica'),(44,18,'2025-08-01 00:00:00',129.000,'Compra',NULL,'Compra generada per coherència històrica'),(45,20,'2025-10-01 00:00:00',65.000,'Compra',NULL,'Compra generada per coherència històrica');
/*!40000 ALTER TABLE `moviment_estoc` ENABLE KEYS */;

--
-- Table structure for table `operari`
--

DROP TABLE IF EXISTS `operari`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operari` (
  `id_operari` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  `carnet_aplicador` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_operari`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operari`
--

/*!40000 ALTER TABLE `operari` DISABLE KEYS */;
INSERT INTO `operari` VALUES (1,'Juan Pérez','APL-001'),(2,'María Gómez','APL-002'),(3,'Carlos Ruiz','APL-003'),(4,'Lucía Torres','APL-004'),(5,'Miguel Sánchez','APL-005'),(6,'Ana López','APL-006'),(7,'David Morales','APL-007'),(8,'Sara Fernández','APL-008'),(9,'Jorge Navarro','APL-009'),(10,'Elena Ramos','APL-010'),(11,'Pedro Castillo','APL-011'),(12,'Laura Medina','APL-012'),(13,'Hugo Martínez','APL-013'),(14,'Patricia Vega','APL-014'),(15,'Alberto Ortiz','APL-015'),(16,'Cristina Núñez','APL-016'),(17,'Sergio Romero','APL-017'),(18,'Natalia Ibáñez','APL-018'),(19,'Óscar Díaz','APL-019'),(20,'Verónica Gil','APL-020');
/*!40000 ALTER TABLE `operari` ENABLE KEYS */;

--
-- Table structure for table `parcela`
--

DROP TABLE IF EXISTS `parcela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parcela` (
  `id_parcela` int(11) NOT NULL AUTO_INCREMENT,
  `ref_cadastral` varchar(50) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `superficie` decimal(10,2) DEFAULT NULL,
  `descripcio` text DEFAULT NULL,
  `municipi` varchar(100) DEFAULT NULL,
  `geometria` geometry NOT NULL,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `edafo` text DEFAULT NULL,
  `documentacio` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `pendent` decimal(5,2) DEFAULT NULL COMMENT 'Percentatge de pendent',
  `orientacio` enum('N','S','E','O','NE','NO','SE','SO') DEFAULT NULL COMMENT 'Orientació dominant',
  `tipus_sol` varchar(100) DEFAULT NULL COMMENT 'Textura o classificació principal del sòl',
  PRIMARY KEY (`id_parcela`),
  UNIQUE KEY `ref_cadastral` (`ref_cadastral`),
  SPATIAL KEY `geometria` (`geometria`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parcela`
--

/*!40000 ALTER TABLE `parcela` DISABLE KEYS */;
INSERT INTO `parcela` VALUES (1,'25172A006000350000SE','Parcel·la nord',4693.35,'Parcel·la de prova','Mollerussa',0xE61000000103000000010000000E000000A1106E237FAFEB3F20CBB0C16ACE4440632DDBDA04B0EB3F9CA38E8E6BCE444075B0913333B4EB3FECEDBF6372CE4440D214765B66B4EB3F10C4A2B772CE44400968C7B205B9EB3F689933457ACE4440BD08D9CD1AB9EB3F09A803687ACE4440FD64150111BCEB3F89AEB53E7FCE4440FB7A0AF5FEC3EB3F44AD87348CCE4440BC690F88CBC2EB3F53785E9792CE4440C120A7B758B4EB3F1098D89378CE4440AADAEA49CBAEEB3F39ADFF666ECE444011114D8CA4AEEB3F9552AC276ECE4440A1106E237FAFEB3F20CBB0C16ACE4440A1106E237FAFEB3F20CBB0C16ACE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.865172929,41.612632953,0 0.865236690,41.612657375,0 0.865747071,41.612865895,0 0.865771464,41.612875895,0 0.866335725,41.613106394,0 0.866345789,41.613110544,0 0.866707327,41.613258208,0 0.867675284,41.613653723,0 0.867528692,41.613848611,0 0.865764960,41.613054734,0 0.865087170,41.612744212,0 0.865068697,41.612736663,0 0.865172929,41.612632953,0 0.865172929,41.612632953,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto1.jpg','Informació edàfica','Informació documental','2025-09-30 07:57:46',0.80,'NE','Al·luvial'),(2,'25172A006000330000SE','Parcel·la sud',33343.68,'Una altra parcel·la','Mollerussa',0xE610000001030000000100000018000000B4CCEA69FCACEB3FB63B69B672CE44408393C02839AEEB3FA922D5AA6FCE444078F35EF94FAEEB3FB57666786FCE444011114D8CA4AEEB3F9552AC276ECE4440AADAEA49CBAEEB3F39ADFF666ECE4440C120A7B758B4EB3F1098D89378CE4440BC690F88CBC2EB3F53785E9792CE44408D2380E29AC7EB3F4E560A569CCE44400F3AC503FACFEB3F199EA3FDABCE44405AAF33CF5BE6EB3FBD014A69D4CE4440E4008FE2FBF6EB3F7EE1BBEBF1CE4440E55636E39CF7EB3F2056CECAF5CE4440F898913FEDF7EB3F2EB71946F8CE4440AE9741DD89F7EB3FDF9A8587FACE4440087374C26FF6EB3F524682C7FCCE44409D03C91007F6EB3F19AC5E9CFDCE444036361DFC22E3EB3F60885D5ADBCE4440476233D4E9CFEB3F84C8B7B1B8CE4440044807009BC7EB3F8FA8466BA9CE444032BDC71035BEEB3F09787EC397CE4440D8F5828465B2EB3F0E94F54A82CE4440DFD704F586ABEB3FD5FD24BD76CE4440B4CCEA69FCACEB3FB63B69B672CE4440B4CCEA69FCACEB3FB63B69B672CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.864866454,41.612875749,0 0.865017490,41.612782816,0 0.865028369,41.612776804,0 0.865068697,41.612736663,0 0.865087170,41.612744212,0 0.865764960,41.613054734,0 0.867528692,41.613848611,0 0.868115847,41.614145999,0 0.869137771,41.614623742,0 0.871869950,41.615857278,0 0.873899405,41.616757838,0 0.873976177,41.616875983,0 0.874014496,41.616951716,0 0.873967106,41.617020550,0 0.873832588,41.617089213,0 0.873782666,41.617114588,0 0.871476643,41.616069122,0 0.869130053,41.615011420,0 0.868115902,41.614545259,0 0.866968663,41.614006459,0 0.865526923,41.613351221,0 0.864688376,41.612998622,0 0.864866454,41.612875749,0 0.864866454,41.612875749,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto2.jpg','Informació edàfica','Informació documental','2025-09-30 07:57:46',1.50,'E','Argilós'),(3,'25172A006000320000SE','Parcel·la 3',9021.52,'Parcela 3 de prueba','Mollerussa',0xE610000001030000000100000008000000C3FD820831AAEB3FF4767C627ACE4440DFD704F586ABEB3FD5FD24BD76CE4440D8F5828465B2EB3F0E94F54A82CE444032BDC71035BEEB3F09787EC397CE4440CB4068B48ABBEB3F1F30CD7CA0CE4440ADDB7FE872A8EB3FA4CAD7357FCE4440C3FD820831AAEB3FF4767C627ACE4440C3FD820831AAEB3FF4767C627ACE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.864525334,41.613109885,0 0.864688376,41.612998622,0 0.865526923,41.613351221,0 0.866968663,41.614006459,0 0.866643288,41.614272690,0 0.864312605,41.613257151,0 0.864525334,41.613109885,0 0.864525334,41.613109885,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto3.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',2.30,'SE','Franc-arenós'),(4,'25172A006000310000SE','Parcel·la 4',4743.08,'Parcela 4 de prueba','Mollerussa',0xE610000001030000000100000007000000ADDB7FE872A8EB3FA4CAD7357FCE4440CB4068B48ABBEB3F1F30CD7CA0CE44402682104759BAEB3F3455C58EA5CE44404303905EFDADEB3FE666B54990CE4440681CCC2107A7EB3F5863734F83CE4440ADDB7FE872A8EB3FA4CAD7357FCE4440ADDB7FE872A8EB3FA4CAD7357FCE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.864312605,41.613257151,0 0.866643288,41.614272690,0 0.866497649,41.614427420,0 0.864988980,41.613778318,0 0.864139143,41.613382274,0 0.864312605,41.613257151,0 0.864312605,41.613257151,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto4.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',3.75,'S','Franc-argilós'),(5,'25172A006000300000SE','Parcel·la 5',2321.19,'Parcela 5 de prueba','Mollerussa',0xE610000001030000000100000007000000403F7775D3A5EB3FAAFB86DD85CE4440681CCC2107A7EB3F5863734F83CE44404303905EFDADEB3FE666B54990CE4440FC4BED6D7CABEB3F841D735C95CE44407332AB56AAA4EB3F6DF84DC988CE4440403F7775D3A5EB3FAAFB86DD85CE4440403F7775D3A5EB3FAAFB86DD85CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.863992433,41.613460246,0 0.864139143,41.613382274,0 0.864988980,41.613778318,0 0.864683356,41.613933140,0 0.863850755,41.613549388,0 0.863992433,41.613460246,0 0.863992433,41.613460246,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto5.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',4.20,'SO','Calcari'),(6,'25172A006000290000SE','Parcel·la 6',28390.28,'Parcela 6 de prueba','Mollerussa',0xE610000001030000000100000012000000CB4068B48ABBEB3F1F30CD7CA0CE444032BDC71035BEEB3F09787EC397CE4440044807009BC7EB3F8FA8466BA9CE4440476233D4E9CFEB3F84C8B7B1B8CE444036361DFC22E3EB3F60885D5ADBCE44409D03C91007F6EB3F19AC5E9CFDCE4440F28EEE483EF4EB3F5DCEC8C7FECE4440C2A98BEB4AF1EB3FCDC0AA7500CF44408FD71E1E0BEFEB3FAFA7FFA700CF44403237F62464EBEB3F42796BD5F8CE44407F108FE5D9DCEB3F53FD9474DECE4440C852DF8AA8CFEB3FE8DB1AC2C5CE4440EC731FFA9AC7EB3FBD0953CEB6CE4440B20DDC813AC5EB3F9CC471F7B1CE4440E6BAD5E527C4EB3F9C401DFEB6CE44402682104759BAEB3F3455C58EA5CE4440CB4068B48ABBEB3F1F30CD7CA0CE4440CB4068B48ABBEB3F1F30CD7CA0CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.866643288,41.614272690,0 0.866968663,41.614006459,0 0.868115902,41.614545259,0 0.869130053,41.615011420,0 0.871476643,41.616069122,0 0.873782666,41.617114588,0 0.873564856,41.617150281,0 0.873204670,41.617201527,0 0.872930106,41.617207527,0 0.872484276,41.616968801,0 0.870709370,41.616163800,0 0.869098922,41.615410102,0 0.868115891,41.614953795,0 0.867825750,41.614806109,0 0.867694806,41.614959492,0 0.866497649,41.614427420,0 0.866643288,41.614272690,0 0.866643288,41.614272690,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto6.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',5.10,'O','Calcari'),(7,'25172A006000280000SE','Parcel·la 7',9214.20,'Parcela 7 de prueba','Mollerussa',0xE61000000103000000010000000D000000B20DDC813AC5EB3F9CC471F7B1CE4440EC731FFA9AC7EB3FBD0953CEB6CE4440C852DF8AA8CFEB3FE8DB1AC2C5CE44407F108FE5D9DCEB3F53FD9474DECE44403237F62464EBEB3F42796BD5F8CE44408FD71E1E0BEFEB3FAFA7FFA700CF4440FEF0864355EDEB3F5C613E7C00CF4440A07576FA8DEBEB3FFA8A2EA2FFCE4440317573CBF0DAEB3F9322D43AE1CE4440F2F0D3F59AC7EB3F318144DBBDCE4440E6BAD5E527C4EB3F9C401DFEB6CE4440B20DDC813AC5EB3F9CC471F7B1CE4440B20DDC813AC5EB3F9CC471F7B1CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.867825750,41.614806109,0 0.868115891,41.614953795,0 0.869098922,41.615410102,0 0.870709370,41.616163800,0 0.872484276,41.616968801,0 0.872930106,41.617207527,0 0.872721321,41.617202311,0 0.872504224,41.617176316,0 0.870476148,41.616248468,0 0.868115883,41.615168961,0 0.867694806,41.614959492,0 0.867825750,41.614806109,0 0.867825750,41.614806109,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto7.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',6.80,'NO','Franc-argilós'),(8,'25172A006000270000SE','Parcel·la 8',31587.11,'Parcela 8 de prueba','Mollerussa',0xE61000000103000000010000004400000008EACEF2C0B0EB3F91E16CCBA4CE44400CF7B5210BB0EB3FCA041A71A3CE44404EE1D617E8AEEB3F5DCDF055A1CE444001C5B6AF7DADEB3F0ED6F00E9FCE44406CBEC34D43ADEB3F886734B29ECE4440DF7E561F63ACEB3FD09E721C9DCE44405AEBDF8344ABEB3FFEADB30D9BCE444030EAC516A3AAEB3F9C10E4E799CE444022E09E54ECA9EB3FFF003B9C98CE44404AF8F77C41A9EB3FD68F576497CE444058C439B5F6A8EB3FCECD04B196CE44409BB5BBD7AEA8EB3FCB41FE0996CE4440BA4AC3651FA8EB3F962163F594CE44400BB183156AA7EB3FD556299393CE4440ABDA7AF50DA6EB3FAAE760DA90CE44409CF057705BA5EB3FB7B457758FCE44400C02AFBFBAA4EB3F7F8FA1338ECE4440183D8C5039A4EB3F4A0120298DCE444009F3FB5786A3EB3F6BBECBBE8BCE4440C867D58EB2A3EB3FC520D6398BCE44407332AB56AAA4EB3F6DF84DC988CE4440FC4BED6D7CABEB3F841D735C95CE44404303905EFDADEB3FE666B54990CE44402682104759BAEB3F3455C58EA5CE4440E6BAD5E527C4EB3F9C401DFEB6CE4440F2F0D3F59AC7EB3F318144DBBDCE4440317573CBF0DAEB3F9322D43AE1CE4440A07576FA8DEBEB3FFA8A2EA2FFCE4440C4B97EA8D1E7EB3F3381C75CFECE44405C6314279EE3EB3F130B778CFDCE44403F2AE9543EE3EB3F99C584CBFCCE44405D2C865704E2EB3FB5C74594FACE44401E9C820E50E0EB3FEF65A5C0F7CE4440DB177FC477DEEB3F2C561EEEF4CE444036D76BA5D0DCEB3F4336A211F2CE444072F83777DADBEB3F9A6B6667F0CE44408E05F79681D9EB3F66DC2A41ECCE444068589E9D92D7EB3F622C61DAE8CE44408BAF9923FBD5EB3F8A6DF717E6CE444092A4D10829D4EB3F27FA0FF8E2CE4440DBCAB0290ED2EB3FC0627242DFCE4440327FB24D1DD0EB3F60EF5EEBDBCE4440299FF0A247CDEB3FE2ECC908D7CE4440CB574646AECBEB3F3D2EDF37D4CE4440E2BA8FC7FFC9EB3F0F792947D1CE4440735DD5B2E5C7EB3FC9C2C8ACCDCE4440B1D1E6F69AC7EB3F645E142BCDCE444014E124A29FC6EB3F1238DF76CBCE444055366A8D5AC4EB3FED7D5E7CC7CE4440A7C6C04400C3EB3FD46A1032C5CE44402778DD37CBC0EB3FBB260760C1CE4440C29F197879C0EB3F805175D4C0CE44401A67996636BFEB3F39F9E3ACBECE4440FABE5AE3A3BDEB3F797269D6BBCE44407EDD1CC0B8BAEB3F29535DA4B6CE444038E0E1E04CB8EB3F57890F72B2CE4440062AFF3497B6EB3F2CC4B855AFCE44405856E622C8B5EB3F8382B5DEADCE4440D6A511F697B4EB3FB7E62E8CABCE44402B37A74B2CB4EB3F439AFFF7AACE4440F6315E742BB4EB3FF63FD7F6AACE4440697F60C10FB4EB3F7C715BBBAACE4440096D3E509CB3EB3F0EF574DBA9CE4440E9DF98DF15B3EB3F7459CBEBA8CE4440059B68C775B2EB3F4E753DDBA7CE44402BD6C949C3B1EB3F316CA990A6CE444008EACEF2C0B0EB3F91E16CCBA4CE444008EACEF2C0B0EB3F91E16CCBA4CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.865326380,41.614404133,0 0.865239683,41.614362848,0 0.865100905,41.614298575,0 0.864928096,41.614229076,0 0.864900257,41.614218021,0 0.864793359,41.614169651,0 0.864656694,41.614106858,0 0.864579720,41.614071833,0 0.864492574,41.614032296,0 0.864411110,41.613995116,0 0.864375452,41.613973739,0 0.864341184,41.613953828,0 0.864272784,41.613920854,0 0.864186327,41.613878627,0 0.864020328,41.613795564,0 0.863935203,41.613753002,0 0.863858580,41.613714651,0 0.863796861,41.613682881,0 0.863711521,41.613639688,0 0.863732604,41.613623838,0 0.863850755,41.613549388,0 0.864683356,41.613933140,0 0.864988980,41.613778318,0 0.866497649,41.614427420,0 0.867694806,41.614959492,0 0.868115883,41.615168961,0 0.870476148,41.616248468,0 0.872504224,41.617176316,0 0.872048215,41.617137525,0 0.871535374,41.617112692,0 0.871489683,41.617089691,0 0.871339961,41.617022070,0 0.871131924,41.616935807,0 0.870906719,41.616849675,0 0.870704959,41.616762356,0 0.870587571,41.616711545,0 0.870301051,41.616584917,0 0.870065029,41.616481111,0 0.869870729,41.616396900,0 0.869648473,41.616301544,0 0.869391519,41.616188341,0 0.869154598,41.616086408,0 0.868808573,41.615937327,0 0.868613374,41.615851387,0 0.868408098,41.615761657,0 0.868151521,41.615651701,0 0.868115885,41.615636239,0 0.867996041,41.615584239,0 0.867718960,41.615462824,0 0.867553839,41.615392931,0 0.867284402,41.615276340,0 0.867245421,41.615259702,0 0.867091370,41.615193950,0 0.866899437,41.615107347,0 0.866543174,41.614948793,0 0.866247596,41.614820726,0 0.866038898,41.614725795,0 0.865940159,41.614681090,0 0.865795117,41.614610217,0 0.865743778,41.614592552,0 0.865743377,41.614592414,0 0.865730169,41.614585323,0 0.865675122,41.614558632,0 0.865611016,41.614530062,0 0.865534677,41.614497571,0 0.865449566,41.614458163,0 0.865326380,41.614404133,0 0.865326380,41.614404133,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto8.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',2.60,'N','Arenós'),(9,'25172A006000260000SE','Parcel·la 9',12861.21,'Parcela 9 de prueba','Mollerussa',0xE61000000103000000010000004700000008EACEF2C0B0EB3F91E16CCBA4CE44402BD6C949C3B1EB3F316CA990A6CE4440059B68C775B2EB3F4E753DDBA7CE4440E9DF98DF15B3EB3F7459CBEBA8CE4440096D3E509CB3EB3F0EF574DBA9CE4440697F60C10FB4EB3F7C715BBBAACE4440F6315E742BB4EB3FF63FD7F6AACE44402B37A74B2CB4EB3F439AFFF7AACE4440D6A511F697B4EB3FB7E62E8CABCE44405856E622C8B5EB3F8382B5DEADCE4440062AFF3497B6EB3F2CC4B855AFCE444038E0E1E04CB8EB3F57890F72B2CE44407EDD1CC0B8BAEB3F29535DA4B6CE4440FABE5AE3A3BDEB3F797269D6BBCE44401A67996636BFEB3F39F9E3ACBECE4440C29F197879C0EB3F805175D4C0CE44402778DD37CBC0EB3FBB260760C1CE4440A7C6C04400C3EB3FD46A1032C5CE444055366A8D5AC4EB3FED7D5E7CC7CE444014E124A29FC6EB3F1238DF76CBCE4440B1D1E6F69AC7EB3F645E142BCDCE4440735DD5B2E5C7EB3FC9C2C8ACCDCE4440E2BA8FC7FFC9EB3F0F792947D1CE4440CB574646AECBEB3F3D2EDF37D4CE4440299FF0A247CDEB3FE2ECC908D7CE4440327FB24D1DD0EB3F60EF5EEBDBCE4440DBCAB0290ED2EB3FC0627242DFCE444092A4D10829D4EB3F27FA0FF8E2CE44408BAF9923FBD5EB3F8A6DF717E6CE444068589E9D92D7EB3F622C61DAE8CE44408E05F79681D9EB3F66DC2A41ECCE444072F83777DADBEB3F9A6B6667F0CE444036D76BA5D0DCEB3F4336A211F2CE4440DB177FC477DEEB3F2C561EEEF4CE44401E9C820E50E0EB3FEF65A5C0F7CE44405D2C865704E2EB3FB5C74594FACE44403F2AE9543EE3EB3F99C584CBFCCE44405C6314279EE3EB3F130B778CFDCE4440884677103BE3EB3FC6994579FDCE444084FBFE567DE1EB3F5EDBE59FFCCE444095FC80EE68DFEB3F7FF6B1EFFBCE4440C83FB207FACEEB3FD5DFA89BDFCE4440FEEA3CED9AC7EB3FE899EFC4D2CE4440735D9D0887B3EB3FF4A1FCBEAECE4440698C6E6E60B3EB3F62174F81AFCE44405590B82AFDB2EB3FE7C0A575B1CE4440DE0472984EACEB3F69FB9E2BA6CE4440ED801152BCACEB3FD0A94AACA3CE4440A10066CBADA1EB3F7E6634E58FCE444052A95B3B26A2EB3FE44E7CC48ECE444070F89D5924A3EB3FEBFD7EE58CCE444009F3FB5786A3EB3F6BBECBBE8BCE4440183D8C5039A4EB3F4A0120298DCE44400C02AFBFBAA4EB3F7F8FA1338ECE44409CF057705BA5EB3FB7B457758FCE4440ABDA7AF50DA6EB3FAAE760DA90CE44400BB183156AA7EB3FD556299393CE4440BA4AC3651FA8EB3F962163F594CE44409BB5BBD7AEA8EB3FCB41FE0996CE444058C439B5F6A8EB3FCECD04B196CE44404AF8F77C41A9EB3FD68F576497CE444022E09E54ECA9EB3FFF003B9C98CE444030EAC516A3AAEB3F9C10E4E799CE44405AEBDF8344ABEB3FFEADB30D9BCE4440DF7E561F63ACEB3FD09E721C9DCE44406CBEC34D43ADEB3F886734B29ECE444001C5B6AF7DADEB3F0ED6F00E9FCE44404EE1D617E8AEEB3F5DCDF055A1CE44400CF7B5210BB0EB3FCA041A71A3CE444008EACEF2C0B0EB3F91E16CCBA4CE444008EACEF2C0B0EB3F91E16CCBA4CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.865326380,41.614404133,0 0.865449566,41.614458163,0 0.865534677,41.614497571,0 0.865611016,41.614530062,0 0.865675122,41.614558632,0 0.865730169,41.614585323,0 0.865743377,41.614592414,0 0.865743778,41.614592552,0 0.865795117,41.614610217,0 0.865940159,41.614681090,0 0.866038898,41.614725795,0 0.866247596,41.614820726,0 0.866543174,41.614948793,0 0.866899437,41.615107347,0 0.867091370,41.615193950,0 0.867245421,41.615259702,0 0.867284402,41.615276340,0 0.867553839,41.615392931,0 0.867718960,41.615462824,0 0.867996041,41.615584239,0 0.868115885,41.615636239,0 0.868151521,41.615651701,0 0.868408098,41.615761657,0 0.868613374,41.615851387,0 0.868808573,41.615937327,0 0.869154598,41.616086408,0 0.869391519,41.616188341,0 0.869648473,41.616301544,0 0.869870729,41.616396900,0 0.870065029,41.616481111,0 0.870301051,41.616584917,0 0.870587571,41.616711545,0 0.870704959,41.616762356,0 0.870906719,41.616849675,0 0.871131924,41.616935807,0 0.871339961,41.617022070,0 0.871489683,41.617089691,0 0.871535374,41.617112692,0 0.871488125,41.617110404,0 0.871275587,41.617084491,0 0.871021715,41.617063486,0 0.869015708,41.616198976,0 0.868115867,41.615807168,0 0.865664975,41.614707826,0 0.865646568,41.614730991,0 0.865599235,41.614790636,0 0.864783571,41.614446118,0 0.864835892,41.614369904,0 0.863486192,41.613766337,0 0.863543621,41.613731919,0 0.863664794,41.613674819,0 0.863711521,41.613639688,0 0.863796861,41.613682881,0 0.863858580,41.613714651,0 0.863935203,41.613753002,0 0.864020328,41.613795564,0 0.864186327,41.613878627,0 0.864272784,41.613920854,0 0.864341184,41.613953828,0 0.864375452,41.613973739,0 0.864411110,41.613995116,0 0.864492574,41.614032296,0 0.864579720,41.614071833,0 0.864656694,41.614106858,0 0.864793359,41.614169651,0 0.864900257,41.614218021,0 0.864928096,41.614229076,0 0.865100905,41.614298575,0 0.865239683,41.614362848,0 0.865326380,41.614404133,0 0.865326380,41.614404133,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto9.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',1.90,'NE','Llimós'),(10,'25172A006000250000SE','Parcel·la 10',12181.83,'Parcela 10 de prueba','Mollerussa',0xE61000000103000000010000001B0000005590B82AFDB2EB3FE7C0A575B1CE4440698C6E6E60B3EB3F62174F81AFCE4440735D9D0887B3EB3FF4A1FCBEAECE4440FEEA3CED9AC7EB3FE899EFC4D2CE4440C83FB207FACEEB3FD5DFA89BDFCE444095FC80EE68DFEB3F7FF6B1EFFBCE4440814CAFF0D5DEEB3FC166E6EDFBCE4440CE8CF5423EDEEB3F7CF1457BFCCE4440E754F0E381DBEB3FB14AD0FCFDCE4440BDC9537C9AD8EB3F0759D7E7F8CE4440C20BB86D33D3EB3FBB446273EFCE4440E970471467D0EB3F9D47BB8DEACE44409D85D050C2CDEB3F1F3364FDE5CE4440879C293AB3C9EB3F828AC1FBDECE4440400A2AEC9AC7EB3F3C69C55DDBCE4440D093DCA8E8C4EB3FDDE4E067D6CE4440DC8F37A075C2EB3FF780BDE5D1CE4440F930C767BCC1EB3FF535DA90D0CE4440B6AA01576DBFEB3F2906E050CCCE44405A3645107FBEEB3FFE505C9ACACE44405EB1A5989ABCEB3FD05B231FC7CE44403FA6D1BE9BB9EB3F7320C89AC1CE4440AE94776B1FB6EB3F85154631BBCE44404D97C87C75B2EB3FE8FFBA72B4CE444011F8939669B2EB3F628FB15CB4CE44405590B82AFDB2EB3FE7C0A575B1CE44405590B82AFDB2EB3FE7C0A575B1CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.865599235,41.614790636,0 0.865646568,41.614730991,0 0.865664975,41.614707826,0 0.868115867,41.615807168,0 0.869015708,41.616198976,0 0.871021715,41.617063486,0 0.870951624,41.617063272,0 0.870879298,41.617080125,0 0.870545335,41.617126085,0 0.870190852,41.616970997,0 0.869531359,41.616682456,0 0.869189777,41.616533009,0 0.868867071,41.616393732,0 0.868371595,41.616179914,0 0.868115865,41.616069528,0 0.867786722,41.615918145,0 0.867487729,41.615780561,0 0.867399409,41.615739924,0 0.867117567,41.615610227,0 0.867003948,41.615557952,0 0.866772936,41.615451710,0 0.866407273,41.615283344,0 0.865981779,41.615087661,0 0.865534538,41.614881841,0 0.865528864,41.614879214,0 0.865599235,41.614790636,0 0.865599235,41.614790636,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto10.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',0.95,'E','Al·luvial'),(11,'25172A006000240000SE','Parcel·la 11',4259.86,'Parcela 11 de prueba','Mollerussa',0xE6100000010300000001000000130000008305C5C9DCA0EB3FDC91AFD991CE4440A10066CBADA1EB3F7E6634E58FCE4440ED801152BCACEB3FD0A94AACA3CE4440DE0472984EACEB3F69FB9E2BA6CE44405590B82AFDB2EB3FE7C0A575B1CE444011F8939669B2EB3F628FB15CB4CE4440470FEBA9AFB0EB3F2E97855DB1CE444020764B1707ACEB3FCD03EB47A9CE44404B872607EEABEB3FB5A37F1CA9CE44404AA0AFC312ABEB3F154FE7D1A7CE44402D0DCE709BAAEB3F931D881DA7CE444076309F0219A7EB3F45B49CD2A1CE444095698F728BA4EB3FCA352BF99DCE4440CB79782FF4A1EB3F5B8AA4109ACE4440C50C6E1AC49FEB3FE5EFE8C396CE4440E2DB68BE529FEB3FF8B1081996CE4440088A06C64D9FEB3F7B781D1196CE44408305C5C9DCA0EB3FDC91AFD991CE44408305C5C9DCA0EB3FDC91AFD991CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.863386530,41.613825999,0 0.863486192,41.613766337,0 0.864835892,41.614369904,0 0.864783571,41.614446118,0 0.865599235,41.614790636,0 0.865528864,41.614879214,0 0.865318138,41.614787760,0 0.864749475,41.614541044,0 0.864737524,41.614535868,0 0.864632971,41.614496458,0 0.864576073,41.614474956,0 0.864147668,41.614313437,0 0.863836025,41.614195963,0 0.863519757,41.614076691,0 0.863252689,41.613975991,0 0.863198635,41.613955621,0 0.863196265,41.613954677,0 0.863386530,41.613825999,0 0.863386530,41.613825999,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','foto11.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',7.40,'SO','Franc-arenós'),(12,'','Parcel·la 12',30279.36,'Parcela 12 de prueba','Mollerussa',0xE61000000103000000010000003B00000008EACEF2C0B0EB3F91E16CCBA4CE44400CF7B5210BB0EB3FCA041A71A3CE44404EE1D617E8AEEB3F5DCDF055A1CE444001C5B6AF7DADEB3F0ED6F00E9FCE44406CBEC34D43ADEB3F886734B29ECE4440DF7E561F63ACEB3FD09E721C9DCE44405AEBDF8344ABEB3FFEADB30D9BCE444030EAC516A3AAEB3F9C10E4E799CE4440572DD7454BAAEB3F6A51884899CE44408E3D767599AAEB3F4D48454597CE444075C557FE26ABEB3FD4B7F2B095CE4440C8CCA5BB62ABEB3F6F46142D95CE4440FC4BED6D7CABEB3F841D735C95CE44404303905EFDADEB3FE666B54990CE44402682104759BAEB3F3455C58EA5CE4440E6BAD5E527C4EB3F9C401DFEB6CE4440F2F0D3F59AC7EB3F318144DBBDCE4440317573CBF0DAEB3F9322D43AE1CE4440A07576FA8DEBEB3FFA8A2EA2FFCE4440C4B97EA8D1E7EB3F3381C75CFECE44405C6314279EE3EB3F130B778CFDCE44403F2AE9543EE3EB3F99C584CBFCCE44405D2C865704E2EB3FB5C74594FACE44401E9C820E50E0EB3FEF65A5C0F7CE4440DB177FC477DEEB3F2C561EEEF4CE444036D76BA5D0DCEB3F4336A211F2CE444072F83777DADBEB3F9A6B6667F0CE44408E05F79681D9EB3F66DC2A41ECCE444068589E9D92D7EB3F622C61DAE8CE44408BAF9923FBD5EB3F8A6DF717E6CE444092A4D10829D4EB3F27FA0FF8E2CE4440DBCAB0290ED2EB3FC0627242DFCE4440327FB24D1DD0EB3F60EF5EEBDBCE4440299FF0A247CDEB3FE2ECC908D7CE4440CB574646AECBEB3F3D2EDF37D4CE4440E2BA8FC7FFC9EB3F0F792947D1CE4440735DD5B2E5C7EB3FC9C2C8ACCDCE4440B1D1E6F69AC7EB3F645E142BCDCE444014E124A29FC6EB3F1238DF76CBCE444055366A8D5AC4EB3FED7D5E7CC7CE4440A7C6C04400C3EB3FD46A1032C5CE44402778DD37CBC0EB3FBB260760C1CE4440C29F197879C0EB3F805175D4C0CE44401A67996636BFEB3F39F9E3ACBECE4440FABE5AE3A3BDEB3F797269D6BBCE44407EDD1CC0B8BAEB3F29535DA4B6CE444038E0E1E04CB8EB3F57890F72B2CE4440062AFF3497B6EB3F2CC4B855AFCE44405856E622C8B5EB3F8382B5DEADCE4440D6A511F697B4EB3FB7E62E8CABCE44402B37A74B2CB4EB3F439AFFF7AACE4440F6315E742BB4EB3FF63FD7F6AACE4440697F60C10FB4EB3F7C715BBBAACE4440096D3E509CB3EB3F0EF574DBA9CE4440E9DF98DF15B3EB3F7459CBEBA8CE4440059B68C775B2EB3F4E753DDBA7CE44402BD6C949C3B1EB3F316CA990A6CE444008EACEF2C0B0EB3F91E16CCBA4CE444008EACEF2C0B0EB3F91E16CCBA4CE4440,'POLYGON((0.865326380 41.614404133, 0.865239683 41.614362848, 0.865100905 41.614298575, 0.864928096 41.614229076, 0.864900257 41.614218021, 0.864793359 41.614169651, 0.864656694 41.614106858, 0.864579720 41.614071833, 0.864537846 41.614052836, 0.864575128 41.613991412, 0.864642617 41.613943213, 0.864671103 41.613927493, 0.864683356 41.613933140, 0.864988980 41.613778318, 0.866497649 41.614427420, 0.867694806 41.614959492, 0.868115883 41.615168961, 0.870476148 41.616248468, 0.872504224 41.617176316, 0.872048215 41.617137525, 0.871535374 41.617112692, 0.871489683 41.617089691, 0.871339961 41.617022070, 0.871131924 41.616935807, 0.870906719 41.616849675, 0.870704959 41.616762356, 0.870587571 41.616711545, 0.870301051 41.616584917, 0.870065029 41.616481111, 0.869870729 41.616396900, 0.869648473 41.616301544, 0.869391519 41.616188341, 0.869154598 41.616086408, 0.868808573 41.615937327, 0.868613374 41.615851387, 0.868408098 41.615761657, 0.868151521 41.615651701, 0.868115885 41.615636239, 0.867996041 41.615584239, 0.867718960 41.615462824, 0.867553839 41.615392931, 0.867284402 41.615276340, 0.867245421 41.615259702, 0.867091370 41.615193950, 0.866899437 41.615107347, 0.866543174 41.614948793, 0.866247596 41.614820726, 0.866038898 41.614725795, 0.865940159 41.614681090, 0.865795117 41.614610217, 0.865743778 41.614592552, 0.865743377 41.614592414, 0.865730169 41.614585323, 0.865675122 41.614558632, 0.865611016 41.614530062, 0.865534677 41.614497571, 0.865449566 41.614458163, 0.865326380 41.614404133, 0.865326380 41.614404133))\r\n','foto12.jpg','Informació edàfica','Informació documental','2025-10-14 08:53:14',3.10,'NO','Pedregós');
/*!40000 ALTER TABLE `parcela` ENABLE KEYS */;

--
-- Table structure for table `pla_fila`
--

DROP TABLE IF EXISTS `pla_fila`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pla_fila` (
  `id_pla` int(11) NOT NULL,
  `id_fila` int(11) NOT NULL,
  PRIMARY KEY (`id_pla`,`id_fila`),
  KEY `fk_pf_fila` (`id_fila`),
  CONSTRAINT `fk_pf_fila` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`),
  CONSTRAINT `fk_pf_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pla_fila`
--

/*!40000 ALTER TABLE `pla_fila` DISABLE KEYS */;
INSERT INTO `pla_fila` VALUES (31,1),(32,2),(33,1),(34,2),(35,1),(36,2),(37,1),(38,2),(39,1),(40,2),(41,3),(42,4),(43,5),(44,6),(45,7),(46,8),(47,9),(48,10),(49,11),(50,12);
/*!40000 ALTER TABLE `pla_fila` ENABLE KEYS */;

--
-- Table structure for table `pla_producte`
--

DROP TABLE IF EXISTS `pla_producte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pla_producte` (
  `id_pla` int(11) NOT NULL,
  `id_producte` int(11) NOT NULL,
  `dosi` varchar(100) NOT NULL,
  `volum_caldo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_pla`,`id_producte`),
  KEY `fk_pp_producte` (`id_producte`),
  CONSTRAINT `fk_pp_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`) ON DELETE CASCADE,
  CONSTRAINT `fk_pp_producte` FOREIGN KEY (`id_producte`) REFERENCES `producte` (`id_producte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pla_producte`
--

/*!40000 ALTER TABLE `pla_producte` DISABLE KEYS */;
INSERT INTO `pla_producte` VALUES (31,1,'200 ml/hl','1000 L'),(32,2,'100 ml/hl','800 L'),(33,3,'500 g/100L','1200 L'),(34,8,'150 g/hl','900 L'),(35,7,'1 L/ha','—'),(36,6,'0.5 L/ha','—'),(37,9,'2 L/ha','—'),(38,4,'2 L/ha','—'),(39,5,'2 kg/ha','—'),(40,10,'0.3 L/ha','—'),(41,11,'vegeu l’etiqueta','1000 L'),(42,12,'vegeu l’etiqueta','1000 L'),(43,13,'vegeu l’etiqueta','1000 L'),(44,14,'vegeu l’etiqueta','1000 L'),(45,15,'vegeu l’etiqueta','1000 L'),(46,16,'vegeu l’etiqueta','1000 L'),(47,17,'vegeu l’etiqueta','1000 L'),(48,18,'vegeu l’etiqueta','1000 L'),(49,19,'vegeu l’etiqueta','1000 L'),(50,20,'vegeu l’etiqueta','1000 L');
/*!40000 ALTER TABLE `pla_producte` ENABLE KEYS */;

--
-- Table structure for table `pla_sector`
--

DROP TABLE IF EXISTS `pla_sector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pla_sector` (
  `id_pla` int(11) NOT NULL,
  `id_sector` int(11) NOT NULL,
  PRIMARY KEY (`id_pla`,`id_sector`),
  KEY `fk_ps_sector` (`id_sector`),
  CONSTRAINT `fk_ps_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`) ON DELETE CASCADE,
  CONSTRAINT `fk_ps_sector` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pla_sector`
--

/*!40000 ALTER TABLE `pla_sector` DISABLE KEYS */;
INSERT INTO `pla_sector` VALUES (31,1),(32,2),(33,1),(34,2),(35,1),(36,2),(37,1),(38,2),(39,1),(40,2),(41,3),(42,4),(43,5),(44,6),(45,7),(46,8),(47,9),(48,10),(49,11),(50,12);
/*!40000 ALTER TABLE `pla_sector` ENABLE KEYS */;

--
-- Table structure for table `pla_tractament`
--

DROP TABLE IF EXISTS `pla_tractament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pla_tractament` (
  `id_pla` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  `tipus` enum('Preventiu','Curatiu') NOT NULL,
  `id_estat_inici` int(11) DEFAULT NULL,
  `id_estat_fi` int(11) DEFAULT NULL,
  `id_varietat` int(11) DEFAULT NULL,
  `finestra_data_inici` date DEFAULT NULL,
  `finestra_data_fi` date DEFAULT NULL,
  `plaga_malaltia_objectiu` varchar(255) DEFAULT NULL,
  `observacions` text DEFAULT NULL,
  PRIMARY KEY (`id_pla`),
  KEY `fk_pla_estat_ini` (`id_estat_inici`),
  KEY `fk_pla_estat_fi` (`id_estat_fi`),
  KEY `fk_pla_varietat` (`id_varietat`),
  CONSTRAINT `fk_pla_estat_fi` FOREIGN KEY (`id_estat_fi`) REFERENCES `estat_fenologic` (`id_estat`),
  CONSTRAINT `fk_pla_estat_ini` FOREIGN KEY (`id_estat_inici`) REFERENCES `estat_fenologic` (`id_estat`),
  CONSTRAINT `fk_pla_varietat` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pla_tractament`
--

/*!40000 ALTER TABLE `pla_tractament` DISABLE KEYS */;
INSERT INTO `pla_tractament` VALUES (31,'Tractament de primavera','Preventiu',1,1,1,'2025-03-01','2025-03-10','Míldiu','Aplicar amb temperatura < 25 °C'),(32,'Control de pugons','Curatiu',1,1,2,'2025-05-10','2025-05-20','Pugó','Repetir si la plaga rebrota'),(33,'Fertilització inicial','Preventiu',1,1,1,'2025-02-01','2025-02-05','Dèficit de nitrogen','Aplicar abans de la brotació'),(34,'Tractament de tardor','Curatiu',1,1,2,'2025-10-01','2025-10-10','Fongs foliars','Aplicar després de la poda'),(35,'Adob orgànic','Preventiu',1,1,1,'2025-09-01','2025-09-10','Millora del sòl','Aplicar compost'),(36,'Tractament d’estiu','Curatiu',1,1,2,'2025-07-01','2025-07-05','Àcar roig','Aplicar acaricida B'),(37,'Micronutrients','Preventiu',1,1,1,'2025-04-01','2025-04-05','Deficiències menors','Zinc i manganès'),(38,'Control de males herbes','Curatiu',1,1,2,'2025-05-01','2025-05-10','Males herbes adventícies','Aplicar herbicida Z'),(39,'Reforç de floració','Preventiu',1,1,1,'2025-03-15','2025-03-20','Floració feble','Afegir calci'),(40,'Manteniment general','Curatiu',1,1,2,'2025-08-01','2025-08-10','Plagues múltiples','Revisió general'),(41,'Prevenció d’oïdi en prefloració (vinya)','Preventiu',11,11,3,'2025-06-01','2025-06-10','Oïdi','Aplicar segons etiqueta; alternar matèries actives per evitar resistències.'),(42,'Control curatiu de mosca de la fruita (cítrics)','Curatiu',12,12,4,'2025-07-01','2025-07-10','Mosca de la fruita','Aplicació amb esquers proteics i suport de trampeig massiu.'),(43,'Programa preventiu contra oïdi (llimoner)','Preventiu',13,13,5,'2025-08-01','2025-08-10','Oïdi','Intercalar fungicides de contacte i sistèmics; respectar terminis de seguretat.'),(44,'Control curatiu de mosca de la fruita (perera)','Curatiu',14,14,6,'2025-09-01','2025-09-10','Mosca de la fruita','Seguiment amb paranys; tractar punts calents.'),(45,'Prevenció d’oïdi (pomer)','Preventiu',15,15,7,'2025-10-01','2025-10-10','Oïdi','Aplicar en brot tendra; evitar hores de màxima insolació.'),(46,'Control curatiu de mosca de la fruita (ametller)','Curatiu',16,16,8,'2025-11-01','2025-11-10','Mosca de la fruita','Esquers + gestió sanitària de fruits caiguts.'),(47,'Prevenció d’oïdi (albercoquer)','Preventiu',17,17,9,'2025-12-01','2025-12-10','Oïdi','Cobertura homogènia a la canòpia.'),(48,'Control curatiu de mosca de la fruita (cirerer)','Curatiu',18,18,10,'2025-01-01','2025-01-10','Mosca de la fruita','Coordinar amb collita; respectar PHI.'),(49,'Prevenció d’oïdi (figuera)','Preventiu',19,19,11,'2025-02-01','2025-02-10','Oïdi','Evitar excés de vigor; millorar aireig.'),(50,'Control curatiu de mosca de la fruita (magraner)','Curatiu',20,20,12,'2025-03-01','2025-03-10','Mosca de la fruita','Rotació d’insecticides i control cultural.');
/*!40000 ALTER TABLE `pla_tractament` ENABLE KEYS */;

--
-- Table structure for table `plantacio`
--

DROP TABLE IF EXISTS `plantacio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plantacio` (
  `id_plantacio` int(11) NOT NULL AUTO_INCREMENT,
  `id_sector` int(11) NOT NULL,
  `id_varietat` int(11) NOT NULL,
  `data_plantacio` date NOT NULL,
  `data_inici` date NOT NULL,
  `data_fi` date DEFAULT NULL,
  `marc_plantacio_files` decimal(5,2) DEFAULT NULL,
  `marc_plantacio_arbres` decimal(5,2) DEFAULT NULL,
  `num_arbres_total` int(11) DEFAULT NULL,
  `origen_material` varchar(255) DEFAULT NULL,
  `certificacions` varchar(255) DEFAULT NULL,
  `sistema_formacio` varchar(100) DEFAULT NULL,
  `inversio_inicial` decimal(12,2) DEFAULT NULL,
  `entrada_produccio_prevista` year(4) DEFAULT NULL,
  `sistema_reg` enum('Goteig','Aspersió','Fertirrigació','Altres') DEFAULT 'Goteig',
  `observacions` text DEFAULT NULL,
  PRIMARY KEY (`id_plantacio`),
  KEY `id_sector` (`id_sector`),
  KEY `id_varietat` (`id_varietat`),
  CONSTRAINT `plantacio_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  CONSTRAINT `plantacio_ibfk_2` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plantacio`
--

/*!40000 ALTER TABLE `plantacio` DISABLE KEYS */;
INSERT INTO `plantacio` VALUES (1,1,1,'2025-02-01','2025-02-01',NULL,3.00,2.00,500,'Viver local','Certif. ECO','Eix central',10000.00,2027,'Goteig',NULL),(2,2,2,'2024-03-01','2024-03-01',NULL,4.00,2.50,400,'Viver regional','Certif. BIO','Vas lliure',8000.00,2026,'Goteig',NULL),(3,3,3,'2024-04-01','2024-04-01',NULL,3.00,3.50,360,'Viver regional','Certif. ECO','Eix central',7900.00,2026,'Goteig',NULL),(4,4,4,'2024-05-01','2024-05-01',NULL,3.50,2.00,380,'Viver regional','Certif. ECO','Vas lliure',8200.00,2027,'Goteig',NULL),(5,5,5,'2024-06-01','2024-06-01',NULL,4.00,2.50,400,'Viver regional','Certif. ECO','Eix central',8500.00,2028,'Goteig',NULL),(6,6,6,'2024-07-01','2024-07-01',NULL,3.00,3.00,420,'Viver regional','Certif. ECO','Vas lliure',8800.00,2026,'Goteig',NULL),(7,7,7,'2024-08-01','2024-08-01',NULL,3.50,3.50,440,'Viver regional','Certif. ECO','Eix central',9100.00,2027,'Goteig',NULL),(8,8,8,'2024-09-01','2024-09-01',NULL,4.00,2.00,460,'Viver regional','Certif. ECO','Vas lliure',9400.00,2028,'Goteig',NULL),(9,9,9,'2024-10-01','2024-10-01',NULL,3.00,2.50,480,'Viver regional','Certif. ECO','Eix central',9700.00,2026,'Goteig',NULL),(10,10,10,'2024-11-01','2024-11-01',NULL,3.50,3.00,500,'Viver regional','Certif. ECO','Vas lliure',10000.00,2027,'Goteig',NULL),(11,11,11,'2024-12-01','2024-12-01',NULL,4.00,3.50,520,'Viver regional','Certif. ECO','Eix central',10300.00,2028,'Goteig',NULL),(12,12,12,'2024-01-01','2024-01-01',NULL,3.00,2.00,540,'Viver regional','Certif. ECO','Vas lliure',10600.00,2026,'Goteig',NULL);
/*!40000 ALTER TABLE `plantacio` ENABLE KEYS */;

--
-- Table structure for table `previsio_collita`
--

DROP TABLE IF EXISTS `previsio_collita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `previsio_collita` (
  `previsio_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parcela` int(11) NOT NULL,
  `id_varietat` int(11) NOT NULL,
  `campanya_any` year(4) NOT NULL,
  `estimacio_produccio` decimal(10,2) DEFAULT NULL,
  `unitat` enum('kg','Tn') NOT NULL DEFAULT 'kg',
  `data_estimada_collita` date DEFAULT NULL,
  `model_predictiu` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`previsio_id`),
  UNIQUE KEY `uq_previsio` (`id_parcela`,`id_varietat`,`campanya_any`),
  KEY `fk_pc_varietat` (`id_varietat`),
  CONSTRAINT `fk_pc_parcela` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pc_varietat` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `previsio_collita`
--

/*!40000 ALTER TABLE `previsio_collita` DISABLE KEYS */;
INSERT INTO `previsio_collita` VALUES (1,1,1,2025,1300.00,'kg','2025-06-10','Rendiment_hist + EDAFO + seguiment'),(2,3,3,2025,950.00,'kg','2025-07-20','Històric + fenologia'),(3,4,4,2025,1600.00,'kg','2025-08-22','Històric + clima');
/*!40000 ALTER TABLE `previsio_collita` ENABLE KEYS */;

--
-- Table structure for table `producte`
--

DROP TABLE IF EXISTS `producte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producte` (
  `id_producte` int(11) NOT NULL AUTO_INCREMENT,
  `tipus` enum('Fitosanitari','Fertilitzant') NOT NULL,
  `nom_comercial` varchar(255) NOT NULL,
  `materia_activa` varchar(255) DEFAULT NULL,
  `concentracio` varchar(100) DEFAULT NULL,
  `espectre_accio` text DEFAULT NULL,
  `cultius_autoritzats` text DEFAULT NULL,
  `dosi_recomendada` varchar(100) DEFAULT NULL,
  `dosi_maxima` varchar(100) DEFAULT NULL,
  `termini_seguretat_dies` int(11) DEFAULT NULL,
  `classificacio_toxicologica` varchar(100) DEFAULT NULL,
  `restriccions_usu` text DEFAULT NULL,
  `compatible_integrada` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_producte`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producte`
--

/*!40000 ALTER TABLE `producte` DISABLE KEYS */;
INSERT INTO `producte` VALUES (1,'Fitosanitari','Fungicida X','Cobre','20%','Control de míldiu','Presseguer, Olivera','200 ml/hl','300 ml/hl',14,'Nociu','Evitar altes temperatures',1),(2,'Fitosanitari','Insecticida A','Imidacloprid','10%','Control de pugons','Olivera','100 ml/hl','150 ml/hl',21,'Tòxic','Prohibit en floració',0),(3,'Fertilitzant','NPK 20-20-20','N-P-K','20%','Creixement general','Tots','500 g/100L','1 kg/100L',0,'No tòxic','',1),(4,'Fitosanitari','Herbicida Z','Glifosato','36%','Control de males herbes','Oliverar','2 L/ha','4 L/ha',30,'Perillós','Evitar el contacte amb sòl humit',0),(5,'Fertilitzant','Calcio Plus','CaCO3','15%','Enfortiment del fruit','Presseguer','2 kg/ha','5 kg/ha',0,'No tòxic','',1),(6,'Fitosanitari','Acaricida B','Abamectina','1.8%','Control d’àcars','Presseguer','0.5 L/ha','1 L/ha',10,'Tòxic','Evitar la sobreaplicació',1),(7,'Fertilitzant','Humic 25','Ácidos húmicos','25%','Millora del sòl','Tots','1 L/ha','2 L/ha',0,'No tòxic','',1),(8,'Fitosanitari','Fungicida C','Mancozeb','80%','Control de fongs','Presseguer','150 g/hl','300 g/hl',14,'Irritante','',1),(9,'Fertilitzant','AminoGrow','Aminoácidos','10%','Millor vigor','Tots','2 L/ha','4 L/ha',0,'No tòxic','',1),(10,'Fitosanitari','Insecticida D','Lambda-cihalotrina','2.5%','Control de orugas','Oliverar','0.3 L/ha','0.5 L/ha',7,'Tòxic','Evitar el contacte amb abelles',1),(11,'Fitosanitari','Fungicida D','Cimoxanilo','5%','Antimíldiu','Vinya','100 g/hl','200 g/hl',10,'Irritante','Respetar plazos',1),(12,'Fertilitzant','Potasa K40','K2O','40%','Aportació de potassi','Tots','20 kg/ha','40 kg/ha',0,'No tòxic','',1),(13,'Fitosanitari','Insecticida E','Spinosad','0.5%','Trips i mosca','Cítrics','0.3 L/ha','0.6 L/ha',7,'Tòxic','Evitar abejas',1),(14,'Fertilitzant','Fosfito P30','Fosfito potásico','30%','Inductor de defenses','Tots','2 L/ha','4 L/ha',0,'No tòxic','',1),(15,'Fitosanitari','Herbicida Selectivo','Fluazifop-P','12.5%','Gramínies','Ametller, Olivera','1 L/ha','2 L/ha',30,'Perillós','Evitar deriva',0),(16,'Fertilitzant','Quelato Fe6','Ferro (EDDHA)','6%','Clorosi fèrrica','Fruvers','4 kg/ha','8 kg/ha',0,'No tòxic','',1),(17,'Fitosanitari','Acaricida C','Fenpiroximato','5%','Ácaros','Fruvers','0.5 L/ha','1 L/ha',10,'Tòxic','No barrejar amb olis',1),(18,'Fertilitzant','BoroPlus','Boro','11%','Quallat de flor','Ametller','1 L/ha','2 L/ha',0,'No tòxic','',1),(19,'Fitosanitari','Fungicida Sistémico','Tebuconazol','25%','Oïdi','Pomer','0.3 L/ha','0.5 L/ha',14,'Irritante','Evitar altes Tª',1),(20,'Fertilitzant','CalMag','Ca+Mg','10%','Equilibri Ca/Mg','Tots','2 L/ha','4 L/ha',0,'No tòxic','',1);
/*!40000 ALTER TABLE `producte` ENABLE KEYS */;

--
-- Table structure for table `producte_lot`
--

DROP TABLE IF EXISTS `producte_lot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producte_lot` (
  `id_lot` int(11) NOT NULL AUTO_INCREMENT,
  `id_producte` int(11) NOT NULL,
  `numero_lot` varchar(100) NOT NULL,
  `data_caducitat` date DEFAULT NULL,
  `id_magatzem` int(11) DEFAULT NULL,
  `quantitat_disponible` decimal(12,3) NOT NULL DEFAULT 0.000,
  `unitat` enum('L','mL','kg','g') NOT NULL,
  `fabricant` varchar(255) DEFAULT NULL,
  `proveidor` varchar(255) DEFAULT NULL,
  `data_compra` date DEFAULT NULL,
  `preu_unitari` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`id_lot`),
  UNIQUE KEY `uq_lot` (`id_producte`,`numero_lot`),
  KEY `fk_pl_magatzem` (`id_magatzem`),
  CONSTRAINT `fk_pl_magatzem` FOREIGN KEY (`id_magatzem`) REFERENCES `magatzem` (`id_magatzem`),
  CONSTRAINT `fk_pl_producte` FOREIGN KEY (`id_producte`) REFERENCES `producte` (`id_producte`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producte_lot`
--

/*!40000 ALTER TABLE `producte_lot` DISABLE KEYS */;
INSERT INTO `producte_lot` VALUES (1,1,'L001','2026-01-10',1,50.000,'L','AgroChem','Proveidor A','2025-01-01',12.5000),(2,2,'L002','2026-03-15',2,40.000,'L','BioFarm','Proveidor B','2025-02-01',15.0000),(3,3,'L003','2027-05-20',3,100.000,'kg','AgroNutrient','Proveidor C','2025-03-01',8.5000),(4,4,'L004','2026-12-31',4,75.000,'L','HerbiMax','Proveidor D','2025-03-20',10.0000),(5,5,'L005','2027-04-10',5,90.000,'kg','CalciCorp','Proveidor E','2025-04-01',9.7500),(6,6,'L006','2026-08-01',6,30.000,'L','AgroChem','Proveidor F','2025-04-10',14.2000),(7,7,'L007','2027-01-15',7,120.000,'L','HumicGrow','Proveidor G','2025-05-01',11.0000),(8,8,'L008','2026-09-09',8,55.000,'kg','MancoFarm','Proveidor H','2025-05-15',10.3000),(9,9,'L009','2027-06-30',9,70.000,'L','AminoPro','Proveidor I','2025-06-01',13.6000),(10,10,'L010','2026-07-25',10,45.000,'L','Insecta','Proveidor J','2025-06-10',16.8000),(11,11,'L011','2027-12-20',11,80.000,'L','Fabricant 11','Proveidor A','2025-01-01',16.0000),(12,12,'L012','2027-01-20',12,100.000,'kg','Fabricant 12','Proveidor B','2025-02-01',17.5000),(13,13,'L013','2027-02-20',13,120.000,'L','Fabricant 13','Proveidor C','2025-03-01',19.0000),(14,14,'L014','2027-03-20',14,140.000,'kg','Fabricant 14','Proveidor D','2025-04-01',10.0000),(15,15,'L015','2027-04-20',15,60.000,'L','Fabricant 15','Proveidor E','2025-05-01',11.5000),(16,16,'L016','2027-05-20',16,80.000,'kg','Fabricant 16','Proveidor F','2025-06-01',13.0000),(17,17,'L017','2027-06-20',17,100.000,'L','Fabricant 17','Proveidor G','2025-07-01',14.5000),(18,18,'L018','2027-07-20',18,120.000,'kg','Fabricant 18','Proveidor H','2025-08-01',16.0000),(19,19,'L019','2027-08-20',19,140.000,'L','Fabricant 19','Proveidor I','2025-09-01',17.5000),(20,20,'L020','2027-09-20',20,60.000,'kg','Fabricant 20','Proveidor J','2025-10-01',19.0000);
/*!40000 ALTER TABLE `producte_lot` ENABLE KEYS */;

--
-- Table structure for table `registre`
--

DROP TABLE IF EXISTS `registre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registre` (
  `id_registre` int(11) NOT NULL AUTO_INCREMENT,
  `id_varietat` int(11) NOT NULL,
  `id_plantacio` int(11) DEFAULT NULL,
  `data_plantacio` date NOT NULL,
  `data_arrencada` date DEFAULT NULL,
  `rendiment` decimal(10,2) DEFAULT NULL,
  `problemes_fitosanitaris` text DEFAULT NULL,
  `id_parcela` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_registre`),
  KEY `id_varietat` (`id_varietat`),
  KEY `id_plantacio` (`id_plantacio`),
  KEY `fk_reg_parcela` (`id_parcela`),
  CONSTRAINT `fk_reg_parcela` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`),
  CONSTRAINT `registre_ibfk_1` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`),
  CONSTRAINT `registre_ibfk_2` FOREIGN KEY (`id_plantacio`) REFERENCES `plantacio` (`id_plantacio`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registre`
--

/*!40000 ALTER TABLE `registre` DISABLE KEYS */;
INSERT INTO `registre` VALUES (1,1,1,'2025-02-01',NULL,2000.00,'Mildiu en primavera',1),(2,2,2,'2024-03-01',NULL,1500.00,'Plaga de pulgón',2),(3,3,3,'2024-09-01',NULL,1610.00,'Sin problemas significativos',3),(4,4,4,'2024-10-01',NULL,1680.00,'Sin problemas significativos',4),(5,5,5,'2024-11-01',NULL,1750.00,'Sin problemas significativos',5),(6,6,6,'2024-12-01',NULL,1820.00,'Sin problemas significativos',6),(7,7,7,'2024-01-01',NULL,1890.00,'Sin problemas significativos',7),(8,8,8,'2024-02-01',NULL,1960.00,'Sin problemas significativos',8),(9,9,9,'2024-03-01',NULL,2030.00,'Sin problemas significativos',9),(10,10,10,'2024-04-01',NULL,2100.00,'Sin problemas significativos',10),(11,11,11,'2024-05-01',NULL,2170.00,'Sin problemas significativos',11),(12,12,12,'2024-06-01',NULL,2240.00,'Sin problemas significativos',12);
/*!40000 ALTER TABLE `registre` ENABLE KEYS */;

--
-- Table structure for table `sector`
--

DROP TABLE IF EXISTS `sector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector` (
  `id_sector` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `superficie` decimal(10,2) DEFAULT NULL,
  `geometria` geometry NOT NULL,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `estat_productiu` enum('Repos','Plantat','Productiu','Reconvertit','Abandonat') DEFAULT 'Plantat',
  PRIMARY KEY (`id_sector`),
  SPATIAL KEY `geometria` (`geometria`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector`
--

/*!40000 ALTER TABLE `sector` DISABLE KEYS */;
INSERT INTO `sector` VALUES (1,'Sector A',5000.00,0xE610000001030000000100000005000000000000000000000000000000000000000000000000000000000000000000F03F000000000000F03F000000000000F03F000000000000F03F000000000000000000000000000000000000000000000000,'<kml></kml>','sectorA.jpg','2025-09-30 07:57:48','Plantat'),(2,'Sector B',4000.00,0xE610000001030000000100000005000000000000000000F03F000000000000F03F000000000000F03F0000000000000040000000000000004000000000000000400000000000000040000000000000F03F000000000000F03F000000000000F03F,'<kml></kml>','sectorB.jpg','2025-09-30 07:57:48','Plantat'),(3,'Sector B',9021.52,0xE610000001030000000100000008000000C3FD820831AAEB3FF4767C627ACE4440DFD704F586ABEB3FD5FD24BD76CE4440D8F5828465B2EB3F0E94F54A82CE444032BDC71035BEEB3F09787EC397CE4440CB4068B48ABBEB3F1F30CD7CA0CE4440ADDB7FE872A8EB3FA4CAD7357FCE4440C3FD820831AAEB3FF4767C627ACE4440C3FD820831AAEB3FF4767C627ACE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.864525334,41.613109885,0 0.864688376,41.612998622,0 0.865526923,41.613351221,0 0.866968663,41.614006459,0 0.866643288,41.614272690,0 0.864312605,41.613257151,0 0.864525334,41.613109885,0 0.864525334,41.613109885,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector3.jpg','2025-10-14 08:53:14','Plantat'),(4,'Sector C',4743.08,0xE610000001030000000100000007000000ADDB7FE872A8EB3FA4CAD7357FCE4440CB4068B48ABBEB3F1F30CD7CA0CE44402682104759BAEB3F3455C58EA5CE44404303905EFDADEB3FE666B54990CE4440681CCC2107A7EB3F5863734F83CE4440ADDB7FE872A8EB3FA4CAD7357FCE4440ADDB7FE872A8EB3FA4CAD7357FCE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.864312605,41.613257151,0 0.866643288,41.614272690,0 0.866497649,41.614427420,0 0.864988980,41.613778318,0 0.864139143,41.613382274,0 0.864312605,41.613257151,0 0.864312605,41.613257151,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector4.jpg','2025-10-14 08:53:14','Plantat'),(5,'Sector D',2321.19,0xE610000001030000000100000007000000403F7775D3A5EB3FAAFB86DD85CE4440681CCC2107A7EB3F5863734F83CE44404303905EFDADEB3FE666B54990CE4440FC4BED6D7CABEB3F841D735C95CE44407332AB56AAA4EB3F6DF84DC988CE4440403F7775D3A5EB3FAAFB86DD85CE4440403F7775D3A5EB3FAAFB86DD85CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.863992433,41.613460246,0 0.864139143,41.613382274,0 0.864988980,41.613778318,0 0.864683356,41.613933140,0 0.863850755,41.613549388,0 0.863992433,41.613460246,0 0.863992433,41.613460246,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector5.jpg','2025-10-14 08:53:14','Plantat'),(6,'Sector E',28390.28,0xE610000001030000000100000012000000CB4068B48ABBEB3F1F30CD7CA0CE444032BDC71035BEEB3F09787EC397CE4440044807009BC7EB3F8FA8466BA9CE4440476233D4E9CFEB3F84C8B7B1B8CE444036361DFC22E3EB3F60885D5ADBCE44409D03C91007F6EB3F19AC5E9CFDCE4440F28EEE483EF4EB3F5DCEC8C7FECE4440C2A98BEB4AF1EB3FCDC0AA7500CF44408FD71E1E0BEFEB3FAFA7FFA700CF44403237F62464EBEB3F42796BD5F8CE44407F108FE5D9DCEB3F53FD9474DECE4440C852DF8AA8CFEB3FE8DB1AC2C5CE4440EC731FFA9AC7EB3FBD0953CEB6CE4440B20DDC813AC5EB3F9CC471F7B1CE4440E6BAD5E527C4EB3F9C401DFEB6CE44402682104759BAEB3F3455C58EA5CE4440CB4068B48ABBEB3F1F30CD7CA0CE4440CB4068B48ABBEB3F1F30CD7CA0CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.866643288,41.614272690,0 0.866968663,41.614006459,0 0.868115902,41.614545259,0 0.869130053,41.615011420,0 0.871476643,41.616069122,0 0.873782666,41.617114588,0 0.873564856,41.617150281,0 0.873204670,41.617201527,0 0.872930106,41.617207527,0 0.872484276,41.616968801,0 0.870709370,41.616163800,0 0.869098922,41.615410102,0 0.868115891,41.614953795,0 0.867825750,41.614806109,0 0.867694806,41.614959492,0 0.866497649,41.614427420,0 0.866643288,41.614272690,0 0.866643288,41.614272690,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector6.jpg','2025-10-14 08:53:14','Plantat'),(7,'Sector F',9214.20,0xE61000000103000000010000000D000000B20DDC813AC5EB3F9CC471F7B1CE4440EC731FFA9AC7EB3FBD0953CEB6CE4440C852DF8AA8CFEB3FE8DB1AC2C5CE44407F108FE5D9DCEB3F53FD9474DECE44403237F62464EBEB3F42796BD5F8CE44408FD71E1E0BEFEB3FAFA7FFA700CF4440FEF0864355EDEB3F5C613E7C00CF4440A07576FA8DEBEB3FFA8A2EA2FFCE4440317573CBF0DAEB3F9322D43AE1CE4440F2F0D3F59AC7EB3F318144DBBDCE4440E6BAD5E527C4EB3F9C401DFEB6CE4440B20DDC813AC5EB3F9CC471F7B1CE4440B20DDC813AC5EB3F9CC471F7B1CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.867825750,41.614806109,0 0.868115891,41.614953795,0 0.869098922,41.615410102,0 0.870709370,41.616163800,0 0.872484276,41.616968801,0 0.872930106,41.617207527,0 0.872721321,41.617202311,0 0.872504224,41.617176316,0 0.870476148,41.616248468,0 0.868115883,41.615168961,0 0.867694806,41.614959492,0 0.867825750,41.614806109,0 0.867825750,41.614806109,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector7.jpg','2025-10-14 08:53:14','Plantat'),(8,'Sector G',31587.11,0xE61000000103000000010000004400000008EACEF2C0B0EB3F91E16CCBA4CE44400CF7B5210BB0EB3FCA041A71A3CE44404EE1D617E8AEEB3F5DCDF055A1CE444001C5B6AF7DADEB3F0ED6F00E9FCE44406CBEC34D43ADEB3F886734B29ECE4440DF7E561F63ACEB3FD09E721C9DCE44405AEBDF8344ABEB3FFEADB30D9BCE444030EAC516A3AAEB3F9C10E4E799CE444022E09E54ECA9EB3FFF003B9C98CE44404AF8F77C41A9EB3FD68F576497CE444058C439B5F6A8EB3FCECD04B196CE44409BB5BBD7AEA8EB3FCB41FE0996CE4440BA4AC3651FA8EB3F962163F594CE44400BB183156AA7EB3FD556299393CE4440ABDA7AF50DA6EB3FAAE760DA90CE44409CF057705BA5EB3FB7B457758FCE44400C02AFBFBAA4EB3F7F8FA1338ECE4440183D8C5039A4EB3F4A0120298DCE444009F3FB5786A3EB3F6BBECBBE8BCE4440C867D58EB2A3EB3FC520D6398BCE44407332AB56AAA4EB3F6DF84DC988CE4440FC4BED6D7CABEB3F841D735C95CE44404303905EFDADEB3FE666B54990CE44402682104759BAEB3F3455C58EA5CE4440E6BAD5E527C4EB3F9C401DFEB6CE4440F2F0D3F59AC7EB3F318144DBBDCE4440317573CBF0DAEB3F9322D43AE1CE4440A07576FA8DEBEB3FFA8A2EA2FFCE4440C4B97EA8D1E7EB3F3381C75CFECE44405C6314279EE3EB3F130B778CFDCE44403F2AE9543EE3EB3F99C584CBFCCE44405D2C865704E2EB3FB5C74594FACE44401E9C820E50E0EB3FEF65A5C0F7CE4440DB177FC477DEEB3F2C561EEEF4CE444036D76BA5D0DCEB3F4336A211F2CE444072F83777DADBEB3F9A6B6667F0CE44408E05F79681D9EB3F66DC2A41ECCE444068589E9D92D7EB3F622C61DAE8CE44408BAF9923FBD5EB3F8A6DF717E6CE444092A4D10829D4EB3F27FA0FF8E2CE4440DBCAB0290ED2EB3FC0627242DFCE4440327FB24D1DD0EB3F60EF5EEBDBCE4440299FF0A247CDEB3FE2ECC908D7CE4440CB574646AECBEB3F3D2EDF37D4CE4440E2BA8FC7FFC9EB3F0F792947D1CE4440735DD5B2E5C7EB3FC9C2C8ACCDCE4440B1D1E6F69AC7EB3F645E142BCDCE444014E124A29FC6EB3F1238DF76CBCE444055366A8D5AC4EB3FED7D5E7CC7CE4440A7C6C04400C3EB3FD46A1032C5CE44402778DD37CBC0EB3FBB260760C1CE4440C29F197879C0EB3F805175D4C0CE44401A67996636BFEB3F39F9E3ACBECE4440FABE5AE3A3BDEB3F797269D6BBCE44407EDD1CC0B8BAEB3F29535DA4B6CE444038E0E1E04CB8EB3F57890F72B2CE4440062AFF3497B6EB3F2CC4B855AFCE44405856E622C8B5EB3F8382B5DEADCE4440D6A511F697B4EB3FB7E62E8CABCE44402B37A74B2CB4EB3F439AFFF7AACE4440F6315E742BB4EB3FF63FD7F6AACE4440697F60C10FB4EB3F7C715BBBAACE4440096D3E509CB3EB3F0EF574DBA9CE4440E9DF98DF15B3EB3F7459CBEBA8CE4440059B68C775B2EB3F4E753DDBA7CE44402BD6C949C3B1EB3F316CA990A6CE444008EACEF2C0B0EB3F91E16CCBA4CE444008EACEF2C0B0EB3F91E16CCBA4CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.865326380,41.614404133,0 0.865239683,41.614362848,0 0.865100905,41.614298575,0 0.864928096,41.614229076,0 0.864900257,41.614218021,0 0.864793359,41.614169651,0 0.864656694,41.614106858,0 0.864579720,41.614071833,0 0.864492574,41.614032296,0 0.864411110,41.613995116,0 0.864375452,41.613973739,0 0.864341184,41.613953828,0 0.864272784,41.613920854,0 0.864186327,41.613878627,0 0.864020328,41.613795564,0 0.863935203,41.613753002,0 0.863858580,41.613714651,0 0.863796861,41.613682881,0 0.863711521,41.613639688,0 0.863732604,41.613623838,0 0.863850755,41.613549388,0 0.864683356,41.613933140,0 0.864988980,41.613778318,0 0.866497649,41.614427420,0 0.867694806,41.614959492,0 0.868115883,41.615168961,0 0.870476148,41.616248468,0 0.872504224,41.617176316,0 0.872048215,41.617137525,0 0.871535374,41.617112692,0 0.871489683,41.617089691,0 0.871339961,41.617022070,0 0.871131924,41.616935807,0 0.870906719,41.616849675,0 0.870704959,41.616762356,0 0.870587571,41.616711545,0 0.870301051,41.616584917,0 0.870065029,41.616481111,0 0.869870729,41.616396900,0 0.869648473,41.616301544,0 0.869391519,41.616188341,0 0.869154598,41.616086408,0 0.868808573,41.615937327,0 0.868613374,41.615851387,0 0.868408098,41.615761657,0 0.868151521,41.615651701,0 0.868115885,41.615636239,0 0.867996041,41.615584239,0 0.867718960,41.615462824,0 0.867553839,41.615392931,0 0.867284402,41.615276340,0 0.867245421,41.615259702,0 0.867091370,41.615193950,0 0.866899437,41.615107347,0 0.866543174,41.614948793,0 0.866247596,41.614820726,0 0.866038898,41.614725795,0 0.865940159,41.614681090,0 0.865795117,41.614610217,0 0.865743778,41.614592552,0 0.865743377,41.614592414,0 0.865730169,41.614585323,0 0.865675122,41.614558632,0 0.865611016,41.614530062,0 0.865534677,41.614497571,0 0.865449566,41.614458163,0 0.865326380,41.614404133,0 0.865326380,41.614404133,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector8.jpg','2025-10-14 08:53:14','Plantat'),(9,'Sector H',12861.21,0xE61000000103000000010000004700000008EACEF2C0B0EB3F91E16CCBA4CE44402BD6C949C3B1EB3F316CA990A6CE4440059B68C775B2EB3F4E753DDBA7CE4440E9DF98DF15B3EB3F7459CBEBA8CE4440096D3E509CB3EB3F0EF574DBA9CE4440697F60C10FB4EB3F7C715BBBAACE4440F6315E742BB4EB3FF63FD7F6AACE44402B37A74B2CB4EB3F439AFFF7AACE4440D6A511F697B4EB3FB7E62E8CABCE44405856E622C8B5EB3F8382B5DEADCE4440062AFF3497B6EB3F2CC4B855AFCE444038E0E1E04CB8EB3F57890F72B2CE44407EDD1CC0B8BAEB3F29535DA4B6CE4440FABE5AE3A3BDEB3F797269D6BBCE44401A67996636BFEB3F39F9E3ACBECE4440C29F197879C0EB3F805175D4C0CE44402778DD37CBC0EB3FBB260760C1CE4440A7C6C04400C3EB3FD46A1032C5CE444055366A8D5AC4EB3FED7D5E7CC7CE444014E124A29FC6EB3F1238DF76CBCE4440B1D1E6F69AC7EB3F645E142BCDCE4440735DD5B2E5C7EB3FC9C2C8ACCDCE4440E2BA8FC7FFC9EB3F0F792947D1CE4440CB574646AECBEB3F3D2EDF37D4CE4440299FF0A247CDEB3FE2ECC908D7CE4440327FB24D1DD0EB3F60EF5EEBDBCE4440DBCAB0290ED2EB3FC0627242DFCE444092A4D10829D4EB3F27FA0FF8E2CE44408BAF9923FBD5EB3F8A6DF717E6CE444068589E9D92D7EB3F622C61DAE8CE44408E05F79681D9EB3F66DC2A41ECCE444072F83777DADBEB3F9A6B6667F0CE444036D76BA5D0DCEB3F4336A211F2CE4440DB177FC477DEEB3F2C561EEEF4CE44401E9C820E50E0EB3FEF65A5C0F7CE44405D2C865704E2EB3FB5C74594FACE44403F2AE9543EE3EB3F99C584CBFCCE44405C6314279EE3EB3F130B778CFDCE4440884677103BE3EB3FC6994579FDCE444084FBFE567DE1EB3F5EDBE59FFCCE444095FC80EE68DFEB3F7FF6B1EFFBCE4440C83FB207FACEEB3FD5DFA89BDFCE4440FEEA3CED9AC7EB3FE899EFC4D2CE4440735D9D0887B3EB3FF4A1FCBEAECE4440698C6E6E60B3EB3F62174F81AFCE44405590B82AFDB2EB3FE7C0A575B1CE4440DE0472984EACEB3F69FB9E2BA6CE4440ED801152BCACEB3FD0A94AACA3CE4440A10066CBADA1EB3F7E6634E58FCE444052A95B3B26A2EB3FE44E7CC48ECE444070F89D5924A3EB3FEBFD7EE58CCE444009F3FB5786A3EB3F6BBECBBE8BCE4440183D8C5039A4EB3F4A0120298DCE44400C02AFBFBAA4EB3F7F8FA1338ECE44409CF057705BA5EB3FB7B457758FCE4440ABDA7AF50DA6EB3FAAE760DA90CE44400BB183156AA7EB3FD556299393CE4440BA4AC3651FA8EB3F962163F594CE44409BB5BBD7AEA8EB3FCB41FE0996CE444058C439B5F6A8EB3FCECD04B196CE44404AF8F77C41A9EB3FD68F576497CE444022E09E54ECA9EB3FFF003B9C98CE444030EAC516A3AAEB3F9C10E4E799CE44405AEBDF8344ABEB3FFEADB30D9BCE4440DF7E561F63ACEB3FD09E721C9DCE44406CBEC34D43ADEB3F886734B29ECE444001C5B6AF7DADEB3F0ED6F00E9FCE44404EE1D617E8AEEB3F5DCDF055A1CE44400CF7B5210BB0EB3FCA041A71A3CE444008EACEF2C0B0EB3F91E16CCBA4CE444008EACEF2C0B0EB3F91E16CCBA4CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.865326380,41.614404133,0 0.865449566,41.614458163,0 0.865534677,41.614497571,0 0.865611016,41.614530062,0 0.865675122,41.614558632,0 0.865730169,41.614585323,0 0.865743377,41.614592414,0 0.865743778,41.614592552,0 0.865795117,41.614610217,0 0.865940159,41.614681090,0 0.866038898,41.614725795,0 0.866247596,41.614820726,0 0.866543174,41.614948793,0 0.866899437,41.615107347,0 0.867091370,41.615193950,0 0.867245421,41.615259702,0 0.867284402,41.615276340,0 0.867553839,41.615392931,0 0.867718960,41.615462824,0 0.867996041,41.615584239,0 0.868115885,41.615636239,0 0.868151521,41.615651701,0 0.868408098,41.615761657,0 0.868613374,41.615851387,0 0.868808573,41.615937327,0 0.869154598,41.616086408,0 0.869391519,41.616188341,0 0.869648473,41.616301544,0 0.869870729,41.616396900,0 0.870065029,41.616481111,0 0.870301051,41.616584917,0 0.870587571,41.616711545,0 0.870704959,41.616762356,0 0.870906719,41.616849675,0 0.871131924,41.616935807,0 0.871339961,41.617022070,0 0.871489683,41.617089691,0 0.871535374,41.617112692,0 0.871488125,41.617110404,0 0.871275587,41.617084491,0 0.871021715,41.617063486,0 0.869015708,41.616198976,0 0.868115867,41.615807168,0 0.865664975,41.614707826,0 0.865646568,41.614730991,0 0.865599235,41.614790636,0 0.864783571,41.614446118,0 0.864835892,41.614369904,0 0.863486192,41.613766337,0 0.863543621,41.613731919,0 0.863664794,41.613674819,0 0.863711521,41.613639688,0 0.863796861,41.613682881,0 0.863858580,41.613714651,0 0.863935203,41.613753002,0 0.864020328,41.613795564,0 0.864186327,41.613878627,0 0.864272784,41.613920854,0 0.864341184,41.613953828,0 0.864375452,41.613973739,0 0.864411110,41.613995116,0 0.864492574,41.614032296,0 0.864579720,41.614071833,0 0.864656694,41.614106858,0 0.864793359,41.614169651,0 0.864900257,41.614218021,0 0.864928096,41.614229076,0 0.865100905,41.614298575,0 0.865239683,41.614362848,0 0.865326380,41.614404133,0 0.865326380,41.614404133,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector9.jpg','2025-10-14 08:53:14','Plantat'),(10,'Sector I',12181.83,0xE61000000103000000010000001B0000005590B82AFDB2EB3FE7C0A575B1CE4440698C6E6E60B3EB3F62174F81AFCE4440735D9D0887B3EB3FF4A1FCBEAECE4440FEEA3CED9AC7EB3FE899EFC4D2CE4440C83FB207FACEEB3FD5DFA89BDFCE444095FC80EE68DFEB3F7FF6B1EFFBCE4440814CAFF0D5DEEB3FC166E6EDFBCE4440CE8CF5423EDEEB3F7CF1457BFCCE4440E754F0E381DBEB3FB14AD0FCFDCE4440BDC9537C9AD8EB3F0759D7E7F8CE4440C20BB86D33D3EB3FBB446273EFCE4440E970471467D0EB3F9D47BB8DEACE44409D85D050C2CDEB3F1F3364FDE5CE4440879C293AB3C9EB3F828AC1FBDECE4440400A2AEC9AC7EB3F3C69C55DDBCE4440D093DCA8E8C4EB3FDDE4E067D6CE4440DC8F37A075C2EB3FF780BDE5D1CE4440F930C767BCC1EB3FF535DA90D0CE4440B6AA01576DBFEB3F2906E050CCCE44405A3645107FBEEB3FFE505C9ACACE44405EB1A5989ABCEB3FD05B231FC7CE44403FA6D1BE9BB9EB3F7320C89AC1CE4440AE94776B1FB6EB3F85154631BBCE44404D97C87C75B2EB3FE8FFBA72B4CE444011F8939669B2EB3F628FB15CB4CE44405590B82AFDB2EB3FE7C0A575B1CE44405590B82AFDB2EB3FE7C0A575B1CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.865599235,41.614790636,0 0.865646568,41.614730991,0 0.865664975,41.614707826,0 0.868115867,41.615807168,0 0.869015708,41.616198976,0 0.871021715,41.617063486,0 0.870951624,41.617063272,0 0.870879298,41.617080125,0 0.870545335,41.617126085,0 0.870190852,41.616970997,0 0.869531359,41.616682456,0 0.869189777,41.616533009,0 0.868867071,41.616393732,0 0.868371595,41.616179914,0 0.868115865,41.616069528,0 0.867786722,41.615918145,0 0.867487729,41.615780561,0 0.867399409,41.615739924,0 0.867117567,41.615610227,0 0.867003948,41.615557952,0 0.866772936,41.615451710,0 0.866407273,41.615283344,0 0.865981779,41.615087661,0 0.865534538,41.614881841,0 0.865528864,41.614879214,0 0.865599235,41.614790636,0 0.865599235,41.614790636,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector10.jpg','2025-10-14 08:53:14','Plantat'),(11,'Sector J',4259.86,0xE6100000010300000001000000130000008305C5C9DCA0EB3FDC91AFD991CE4440A10066CBADA1EB3F7E6634E58FCE4440ED801152BCACEB3FD0A94AACA3CE4440DE0472984EACEB3F69FB9E2BA6CE44405590B82AFDB2EB3FE7C0A575B1CE444011F8939669B2EB3F628FB15CB4CE4440470FEBA9AFB0EB3F2E97855DB1CE444020764B1707ACEB3FCD03EB47A9CE44404B872607EEABEB3FB5A37F1CA9CE44404AA0AFC312ABEB3F154FE7D1A7CE44402D0DCE709BAAEB3F931D881DA7CE444076309F0219A7EB3F45B49CD2A1CE444095698F728BA4EB3FCA352BF99DCE4440CB79782FF4A1EB3F5B8AA4109ACE4440C50C6E1AC49FEB3FE5EFE8C396CE4440E2DB68BE529FEB3FF8B1081996CE4440088A06C64D9FEB3F7B781D1196CE44408305C5C9DCA0EB3FDC91AFD991CE44408305C5C9DCA0EB3FDC91AFD991CE4440,'<Polygon><outerBoundaryIs><LinearRing><coordinates>0.863386530,41.613825999,0 0.863486192,41.613766337,0 0.864835892,41.614369904,0 0.864783571,41.614446118,0 0.865599235,41.614790636,0 0.865528864,41.614879214,0 0.865318138,41.614787760,0 0.864749475,41.614541044,0 0.864737524,41.614535868,0 0.864632971,41.614496458,0 0.864576073,41.614474956,0 0.864147668,41.614313437,0 0.863836025,41.614195963,0 0.863519757,41.614076691,0 0.863252689,41.613975991,0 0.863198635,41.613955621,0 0.863196265,41.613954677,0 0.863386530,41.613825999,0 0.863386530,41.613825999,0</coordinates></LinearRing></outerBoundaryIs></Polygon>','sector11.jpg','2025-10-14 08:53:14','Plantat'),(12,'Sector K',30279.36,0xE61000000103000000010000003B00000008EACEF2C0B0EB3F91E16CCBA4CE44400CF7B5210BB0EB3FCA041A71A3CE44404EE1D617E8AEEB3F5DCDF055A1CE444001C5B6AF7DADEB3F0ED6F00E9FCE44406CBEC34D43ADEB3F886734B29ECE4440DF7E561F63ACEB3FD09E721C9DCE44405AEBDF8344ABEB3FFEADB30D9BCE444030EAC516A3AAEB3F9C10E4E799CE4440572DD7454BAAEB3F6A51884899CE44408E3D767599AAEB3F4D48454597CE444075C557FE26ABEB3FD4B7F2B095CE4440C8CCA5BB62ABEB3F6F46142D95CE4440FC4BED6D7CABEB3F841D735C95CE44404303905EFDADEB3FE666B54990CE44402682104759BAEB3F3455C58EA5CE4440E6BAD5E527C4EB3F9C401DFEB6CE4440F2F0D3F59AC7EB3F318144DBBDCE4440317573CBF0DAEB3F9322D43AE1CE4440A07576FA8DEBEB3FFA8A2EA2FFCE4440C4B97EA8D1E7EB3F3381C75CFECE44405C6314279EE3EB3F130B778CFDCE44403F2AE9543EE3EB3F99C584CBFCCE44405D2C865704E2EB3FB5C74594FACE44401E9C820E50E0EB3FEF65A5C0F7CE4440DB177FC477DEEB3F2C561EEEF4CE444036D76BA5D0DCEB3F4336A211F2CE444072F83777DADBEB3F9A6B6667F0CE44408E05F79681D9EB3F66DC2A41ECCE444068589E9D92D7EB3F622C61DAE8CE44408BAF9923FBD5EB3F8A6DF717E6CE444092A4D10829D4EB3F27FA0FF8E2CE4440DBCAB0290ED2EB3FC0627242DFCE4440327FB24D1DD0EB3F60EF5EEBDBCE4440299FF0A247CDEB3FE2ECC908D7CE4440CB574646AECBEB3F3D2EDF37D4CE4440E2BA8FC7FFC9EB3F0F792947D1CE4440735DD5B2E5C7EB3FC9C2C8ACCDCE4440B1D1E6F69AC7EB3F645E142BCDCE444014E124A29FC6EB3F1238DF76CBCE444055366A8D5AC4EB3FED7D5E7CC7CE4440A7C6C04400C3EB3FD46A1032C5CE44402778DD37CBC0EB3FBB260760C1CE4440C29F197879C0EB3F805175D4C0CE44401A67996636BFEB3F39F9E3ACBECE4440FABE5AE3A3BDEB3F797269D6BBCE44407EDD1CC0B8BAEB3F29535DA4B6CE444038E0E1E04CB8EB3F57890F72B2CE4440062AFF3497B6EB3F2CC4B855AFCE44405856E622C8B5EB3F8382B5DEADCE4440D6A511F697B4EB3FB7E62E8CABCE44402B37A74B2CB4EB3F439AFFF7AACE4440F6315E742BB4EB3FF63FD7F6AACE4440697F60C10FB4EB3F7C715BBBAACE4440096D3E509CB3EB3F0EF574DBA9CE4440E9DF98DF15B3EB3F7459CBEBA8CE4440059B68C775B2EB3F4E753DDBA7CE44402BD6C949C3B1EB3F316CA990A6CE444008EACEF2C0B0EB3F91E16CCBA4CE444008EACEF2C0B0EB3F91E16CCBA4CE4440,'POLYGON((0.865326380 41.614404133, 0.865239683 41.614362848, 0.865100905 41.614298575, 0.864928096 41.614229076, 0.864900257 41.614218021, 0.864793359 41.614169651, 0.864656694 41.614106858, 0.864579720 41.614071833, 0.864537846 41.614052836, 0.864575128 41.613991412, 0.864642617 41.613943213, 0.864671103 41.613927493, 0.864683356 41.613933140, 0.864988980 41.613778318, 0.866497649 41.614427420, 0.867694806 41.614959492, 0.868115883 41.615168961, 0.870476148 41.616248468, 0.872504224 41.617176316, 0.872048215 41.617137525, 0.871535374 41.617112692, 0.871489683 41.617089691, 0.871339961 41.617022070, 0.871131924 41.616935807, 0.870906719 41.616849675, 0.870704959 41.616762356, 0.870587571 41.616711545, 0.870301051 41.616584917, 0.870065029 41.616481111, 0.869870729 41.616396900, 0.869648473 41.616301544, 0.869391519 41.616188341, 0.869154598 41.616086408, 0.868808573 41.615937327, 0.868613374 41.615851387, 0.868408098 41.615761657, 0.868151521 41.615651701, 0.868115885 41.615636239, 0.867996041 41.615584239, 0.867718960 41.615462824, 0.867553839 41.615392931, 0.867284402 41.615276340, 0.867245421 41.615259702, 0.867091370 41.615193950, 0.866899437 41.615107347, 0.866543174 41.614948793, 0.866247596 41.614820726, 0.866038898 41.614725795, 0.865940159 41.614681090, 0.865795117 41.614610217, 0.865743778 41.614592552, 0.865743377 41.614592414, 0.865730169 41.614585323, 0.865675122 41.614558632, 0.865611016 41.614530062, 0.865534677 41.614497571, 0.865449566 41.614458163, 0.865326380 41.614404133, 0.865326380 41.614404133))\n','sector12.jpg','2025-10-14 08:53:14','Plantat');
/*!40000 ALTER TABLE `sector` ENABLE KEYS */;

--
-- Table structure for table `sector_parcela`
--

DROP TABLE IF EXISTS `sector_parcela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector_parcela` (
  `id_sector` int(11) NOT NULL,
  `id_parcela` int(11) NOT NULL,
  PRIMARY KEY (`id_sector`,`id_parcela`),
  KEY `idx_sp_parcela` (`id_parcela`),
  CONSTRAINT `sp_fk_parcela` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`),
  CONSTRAINT `sp_fk_sector` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector_parcela`
--

/*!40000 ALTER TABLE `sector_parcela` DISABLE KEYS */;
INSERT INTO `sector_parcela` VALUES (1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(12,12);
/*!40000 ALTER TABLE `sector_parcela` ENABLE KEYS */;

--
-- Table structure for table `sector_sol`
--

DROP TABLE IF EXISTS `sector_sol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector_sol` (
  `id_sector` int(11) NOT NULL,
  `id_sol` int(11) NOT NULL,
  PRIMARY KEY (`id_sector`,`id_sol`),
  KEY `id_sol` (`id_sol`),
  CONSTRAINT `sector_sol_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  CONSTRAINT `sector_sol_ibfk_2` FOREIGN KEY (`id_sol`) REFERENCES `sol` (`id_sol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector_sol`
--

/*!40000 ALTER TABLE `sector_sol` DISABLE KEYS */;
INSERT INTO `sector_sol` VALUES (1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(12,12);
/*!40000 ALTER TABLE `sector_sol` ENABLE KEYS */;

--
-- Table structure for table `sector_varietat`
--

DROP TABLE IF EXISTS `sector_varietat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector_varietat` (
  `id_sector` int(11) NOT NULL,
  `id_varietat` int(11) NOT NULL,
  `id_data` int(11) NOT NULL,
  PRIMARY KEY (`id_sector`,`id_varietat`,`id_data`),
  KEY `id_varietat` (`id_varietat`),
  KEY `id_data` (`id_data`),
  CONSTRAINT `sector_varietat_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  CONSTRAINT `sector_varietat_ibfk_2` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`),
  CONSTRAINT `sector_varietat_ibfk_3` FOREIGN KEY (`id_data`) REFERENCES `data` (`id_data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector_varietat`
--

/*!40000 ALTER TABLE `sector_varietat` DISABLE KEYS */;
INSERT INTO `sector_varietat` VALUES (1,1,1),(2,2,2),(3,3,3),(4,4,4),(5,5,5),(6,6,6),(7,7,7),(8,8,8),(9,9,9),(10,10,10),(11,11,11),(12,12,12);
/*!40000 ALTER TABLE `sector_varietat` ENABLE KEYS */;

--
-- Table structure for table `seguiment`
--

DROP TABLE IF EXISTS `seguiment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguiment` (
  `id_seguiment` int(11) NOT NULL AUTO_INCREMENT,
  `id_sector` int(11) NOT NULL,
  `data_registre` date NOT NULL,
  `estat_fenologic` varchar(255) DEFAULT NULL,
  `creixement_vegetatiu` text DEFAULT NULL,
  `incidencies_detectades` text DEFAULT NULL,
  `intervencions_realitzades` text DEFAULT NULL,
  `estimacio_actualizada_collita` decimal(10,2) DEFAULT NULL,
  `id_plantacio` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_seguiment`),
  KEY `id_sector` (`id_sector`),
  KEY `fk_seg_plantacio` (`id_plantacio`),
  CONSTRAINT `fk_seg_plantacio` FOREIGN KEY (`id_plantacio`) REFERENCES `plantacio` (`id_plantacio`),
  CONSTRAINT `seguiment_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguiment`
--

/*!40000 ALTER TABLE `seguiment` DISABLE KEYS */;
INSERT INTO `seguiment` VALUES (1,1,'2025-03-01','Floració','Brots vigorosos','Míldiu incipient','Tractament preventiu',1800.00,1),(2,2,'2024-06-10','Fructificació','Normal','Ataque de pulgón','Aplicació d’insecticida',1600.00,2),(3,3,'2025-05-10','Floració','Normal','Sense incidències','Revisió setmanal',1650.00,3),(4,4,'2025-06-10','Quallat','Normal','Sense incidències','Revisió setmanal',1700.00,4),(5,5,'2025-07-10','Fructificació','Normal','Sense incidències','Revisió setmanal',1750.00,5),(6,6,'2025-08-10','Engreix','Normal','Sense incidències','Revisió setmanal',1800.00,6),(7,7,'2025-09-10','Enverol','Normal','Sense incidències','Revisió setmanal',1850.00,7),(8,8,'2025-10-10','Maduració','Normal','Sense incidències','Revisió setmanal',1900.00,8),(9,9,'2025-11-10','Postcollita','Normal','Sense incidències','Revisió setmanal',1950.00,9),(10,10,'2025-12-10','Repòs','Normal','Sense incidències','Revisió setmanal',2000.00,10),(11,11,'2025-01-10','Brotació','Normal','Sense incidències','Revisió setmanal',2050.00,11),(12,12,'2025-02-10','Vigorització','Normal','Sense incidències','Revisió setmanal',2100.00,12);
/*!40000 ALTER TABLE `seguiment` ENABLE KEYS */;

--
-- Table structure for table `sol`
--

DROP TABLE IF EXISTS `sol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sol` (
  `id_sol` int(11) NOT NULL AUTO_INCREMENT,
  `tipus` varchar(100) NOT NULL,
  `ph` decimal(4,2) DEFAULT NULL,
  `materia_organica` decimal(5,2) DEFAULT NULL,
  `observacions` text DEFAULT NULL,
  PRIMARY KEY (`id_sol`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sol`
--

/*!40000 ALTER TABLE `sol` DISABLE KEYS */;
INSERT INTO `sol` VALUES (1,'Arenós',6.50,2.50,'Bon drenatge'),(2,'Argilós',7.20,3.10,'Alta retenció d’aigua'),(3,'Franc',6.80,2.80,'Equilibrat'),(4,'Franc-arenós',6.20,2.30,'Bon drenatge'),(5,'Franc-argilós',7.40,3.50,'Fèrtil, pesant'),(6,'Calcari',8.10,1.80,'Alt CaCO₃'),(7,'Llimós',6.90,3.20,'Reté humitat'),(8,'Turbós',5.50,8.00,'Ric en MO'),(9,'Salí',8.30,1.20,'Conductivitat alta'),(10,'Pedregós',7.00,1.00,'Bon drenatge, baixa retenció'),(11,'Volcànic',6.50,4.50,'Minerals disponibles'),(12,'Al·luvial',7.10,2.90,'Fèrtil i profund');
/*!40000 ALTER TABLE `sol` ENABLE KEYS */;

--
-- Table structure for table `tractament`
--

DROP TABLE IF EXISTS `tractament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tractament` (
  `id_tractament` int(11) NOT NULL AUTO_INCREMENT,
  `id_fila` int(11) NOT NULL,
  `data` date NOT NULL,
  `tipus` enum('Fitosanitari','Fertilitzant','Altre') DEFAULT NULL,
  `producte` varchar(255) DEFAULT NULL,
  `dosi_ml` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_tractament`),
  KEY `id_fila` (`id_fila`),
  CONSTRAINT `tractament_ibfk_1` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tractament`
--

/*!40000 ALTER TABLE `tractament` DISABLE KEYS */;
INSERT INTO `tractament` VALUES (1,1,'2025-04-01','Fitosanitari','Fungicida X','200 ml'),(2,2,'2025-05-15','Fertilitzant','NPK 20-20-20','500 ml'),(3,3,'2025-07-15','Fitosanitari','Fungicida D','200 ml'),(4,4,'2025-08-15','Fertilitzant','NPK 20-20-20','500 ml'),(5,5,'2025-09-15','Altre','Rentat de fulles','—'),(6,6,'2025-10-15','Fitosanitari','Fungicida D','200 ml'),(7,7,'2025-11-15','Fertilitzant','NPK 20-20-20','500 ml'),(8,8,'2025-12-15','Altre','Rentat de fulles','—'),(9,9,'2025-01-15','Fitosanitari','Fungicida D','200 ml'),(10,10,'2025-02-15','Fertilitzant','NPK 20-20-20','500 ml'),(11,11,'2025-03-15','Altre','Rentat de fulles','—'),(12,12,'2025-04-15','Fitosanitari','Fungicida D','200 ml');
/*!40000 ALTER TABLE `tractament` ENABLE KEYS */;

--
-- Temporary view structure for view `v_collita_plantacio`
--

DROP TABLE IF EXISTS `v_collita_plantacio`;
/*!50001 DROP VIEW IF EXISTS `v_collita_plantacio`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_collita_plantacio` AS SELECT 
 1 AS `collita_id`,
 1 AS `plantacio_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `varietat`
--

DROP TABLE IF EXISTS `varietat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `varietat` (
  `id_varietat` int(11) NOT NULL AUTO_INCREMENT,
  `id_especie` int(11) NOT NULL,
  `nom_cientific` varchar(255) NOT NULL,
  `nom_comu` varchar(255) NOT NULL,
  `caracteristiques_agronomiques` text DEFAULT NULL,
  `cicle_vegetatiu` text DEFAULT NULL,
  `requisits_pollinitzacio` text DEFAULT NULL,
  `productivitat_mitjana` decimal(10,2) DEFAULT NULL,
  `qualitats_organoleptiques` text DEFAULT NULL,
  PRIMARY KEY (`id_varietat`),
  KEY `id_especie` (`id_especie`),
  CONSTRAINT `varietat_ibfk_1` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `varietat`
--

/*!40000 ALTER TABLE `varietat` DISABLE KEYS */;
INSERT INTO `varietat` VALUES (1,1,'Prunus persica var. platycarpa','Paraguayo','Resistent al fred','Caducifoli','Necessita pol·linitzador',2500.50,'Dolç i sucós'),(2,2,'Olea europaea var. arbequina','Arbequina','Alta productivitat','Perennifoli','Autopol·linitzant',1800.00,'Oli suau'),(3,3,'Vitis vinifera var. tempranillo','Tempranillo','Bona qualitat en climes mediterranis','Perennifoli','Pol·linització entomòfila',9000.00,'Aromes a fruits rojos'),(4,4,'Citrus sinensis var. navelina','Navelina','Alta productivitat','Perennifoli','Autopol·linitzant',25000.00,'Dolç, sense llavors'),(5,5,'Citrus limon var. eureka','Eureka','Floració gairebé contínua','Perennifoli','Autopol·linitzant',18000.00,'Aroma intenso'),(6,6,'Pyrus communis var. conference','Conference','Bon quallat','Caducifoli','Parcialment autoincompatible',15000.00,'Textura mantegosa'),(7,7,'Malus domestica var. golden','Golden','Sensible a l’oïdi','Caducifoli','Requereix pol·linitzador',30000.00,'Dolç, cruixent'),(8,8,'Prunus dulcis var. marcona','Marcona','Molt apreciada per la qualitat','Caducifoli','Autoincompatible',1200.00,'Gra dolç'),(9,9,'Prunus armeniaca var. bulida','Bulida','Precoç','Caducifoli','Autoincompatible',10000.00,'Gust equilibrat'),(10,10,'Prunus avium var. burlat','Burlat','Primerenca','Caducifoli','Requereix pol·linitzador',8000.00,'Dolça, ferma'),(11,11,'Ficus carica var. brown turkey','Brown Turkey','Tolerant a la sequera','Caducifoli','Partenocàrpic parcial',6000.00,'Dolça, melosa'),(12,12,'Punica granatum var. mollar','Mollar','Bona adaptació','Caducifoli','Autopol·linitzant',9000.00,'Grans suaus');
/*!40000 ALTER TABLE `varietat` ENABLE KEYS */;

--
-- Dumping routines for database 'projecte_sintesis'
--

--
-- Final view structure for view `v_collita_plantacio`
--

/*!50001 DROP VIEW IF EXISTS `v_collita_plantacio`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_collita_plantacio` AS select distinct `cf`.`collita_id` AS `collita_id`,`f`.`id_increment` AS `plantacio_id` from (`collita_fila` `cf` join `fila` `f` on(`f`.`id_fila` = `cf`.`id_fila`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-10 11:06:53
