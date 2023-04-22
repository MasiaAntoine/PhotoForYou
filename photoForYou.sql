-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sab_photoforyou
-- ------------------------------------------------------
-- Server version	5.7.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `boutique`
--

DROP TABLE IF EXISTS `boutique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boutique` (
  `idArticle` int(11) NOT NULL AUTO_INCREMENT,
  `creditGiveArticle` int(11) NOT NULL,
  `titleArticle` varchar(60) CHARACTER SET latin1 NOT NULL,
  `descriptionArticle` mediumtext CHARACTER SET latin1 NOT NULL,
  `priceArticle` decimal(9,2) NOT NULL,
  PRIMARY KEY (`idArticle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boutique`
--

LOCK TABLES `boutique` WRITE;
/*!40000 ALTER TABLE `boutique` DISABLE KEYS */;
INSERT INTO `boutique` VALUES (1,2,'poignet de crédits','Reçois 2 crédits à utiliser dans la galerie photo.',10.00),(2,4,'sac de crédits','Reçois 4 crédits à utiliser dans la galerie photo.',20.00),(3,6,'coffre de crédits','Reçois 6 crédits à utiliser dans la galerie photo.',30.00);
/*!40000 ALTER TABLE `boutique` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `globals`
--

DROP TABLE IF EXISTS `globals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `globals` (
  `remise` decimal(9,2) DEFAULT '0.00',
  `costs` decimal(9,2) DEFAULT '0.00',
  `tva` decimal(9,2) DEFAULT '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `globals`
--

LOCK TABLES `globals` WRITE;
/*!40000 ALTER TABLE `globals` DISABLE KEYS */;
INSERT INTO `globals` VALUES (0.00,50.00,5.50);
/*!40000 ALTER TABLE `globals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log` (
  `date` datetime NOT NULL,
  `table_name` varchar(45) COLLATE utf8_bin NOT NULL,
  `type` varchar(45) COLLATE utf8_bin NOT NULL,
  `detail` varchar(45) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES ('2023-04-14 21:16:26','tag','A','{\"idTag\": 112}'),('2023-04-14 21:16:52','tag','D','{\"idTag\": 112}'),('2023-04-14 21:16:55','tag','D','{\"idTag\": 111}'),('2023-04-14 21:17:34','tag','A','{\"idTag\": 113}'),('2023-04-14 21:17:38','tag','M','{\"idTag\": 113}'),('2023-04-14 21:17:46','tag','D','{\"idTag\": 113}'),('2023-04-14 21:18:31','user','A','{\"idUser\": 19, \"rankUser\": 2}'),('2023-04-14 21:18:56','user','M','{\"idUser\": 19, \"reason\": \"ban\"}'),('2023-04-14 21:19:02','user','M','{\"idUser\": 19, \"reason\": \"unban\"}'),('2023-04-14 21:19:23','user','M','{\"idUser\": 19, \"reason\": \"ban\"}'),('2023-04-14 21:19:32','user','D','{\"idUser\": 19, \"rankUser\": 2}'),('2023-04-14 22:16:11','photo','A','{\"idPhoto\": 69, \"idPhotographe\": 15}'),('2023-04-14 22:16:29','photo','D','{\"idPhoto\": 69, \"idPhotographe\": 15}');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagesview`
--

DROP TABLE IF EXISTS `pagesview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagesview` (
  `namePage` varchar(100) COLLATE utf8_bin NOT NULL,
  `idSession` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `idUser` int(11) NOT NULL,
  `dateVisit` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagesview`
--

LOCK TABLES `pagesview` WRITE;
/*!40000 ALTER TABLE `pagesview` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagesview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photo`
--

DROP TABLE IF EXISTS `photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photo` (
  `idPhoto` int(11) NOT NULL AUTO_INCREMENT,
  `creditPricePhoto` decimal(9,2) NOT NULL,
  `isBuyPhoto` int(11) DEFAULT NULL,
  `titlePhoto` varchar(70) NOT NULL,
  `descriptionPhoto` mediumtext NOT NULL,
  `datePublicPhoto` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idUserPhotographer` int(11) NOT NULL,
  PRIMARY KEY (`idPhoto`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photo`
--

LOCK TABLES `photo` WRITE;
/*!40000 ALTER TABLE `photo` DISABLE KEYS */;
INSERT INTO `photo` VALUES (50,3.40,NULL,'Sami le singe','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-13 11:07:14',15),(55,10.13,NULL,'femme avec des fleurs','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-10 11:26:45',11),(48,2.00,NULL,'lever de soleil dans un chambre d\'hotel','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type.','2023-01-23 11:05:17',15),(49,2.32,17,'oiseau qui mange des fruits','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-18 11:06:18',15),(51,5.26,NULL,'montagne de neige','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-17 11:08:21',15),(54,7.10,NULL,'rose bleu','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-19 11:25:45',11),(56,7.04,NULL,'chat cacher','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-02 11:27:15',11),(57,3.84,NULL,'enfant main dans la main','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-22 11:28:03',11),(58,3.40,NULL,'papa avec son bébé','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-14 11:28:48',11),(59,2.89,NULL,'femme naturelle','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-09 11:29:51',11),(60,4.72,NULL,'femme zen sur un coucher de soleil','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-16 11:31:51',11),(61,2.22,NULL,'fleurs de coquelicot rouge','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-11 11:35:14',16),(62,5.13,NULL,'orchidée fleurs rose','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-18 11:35:37',16),(63,5.92,NULL,'femme vu sur soleil dans un champ d\'herbe','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-09 11:36:10',16),(64,6.50,NULL,'enfant souriant','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-23 11:37:01',16),(65,4.73,NULL,'petite fille enfant triste avec nounours','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-02 11:37:20',16),(66,4.00,NULL,'petite souris mignonne','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-02 11:37:44',16),(67,3.00,NULL,'flamant rose','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-03 11:38:01',16),(68,5.00,NULL,'fleurs rose et blanche','lorem ipsum is simply dummy text of the printing and typesetting industry. lorem ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a','2023-01-07 11:38:17',16);
/*!40000 ALTER TABLE `photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rank`
--

DROP TABLE IF EXISTS `rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rank` (
  `idRank` int(11) NOT NULL AUTO_INCREMENT,
  `nameRank` varchar(60) NOT NULL,
  PRIMARY KEY (`idRank`),
  UNIQUE KEY `nameRank_UNIQUE` (`nameRank`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rank`
--

LOCK TABLES `rank` WRITE;
/*!40000 ALTER TABLE `rank` DISABLE KEYS */;
INSERT INTO `rank` VALUES (1,'client'),(2,'photographe'),(3,'admin');
/*!40000 ALTER TABLE `rank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag` (
  `idTag` int(11) NOT NULL AUTO_INCREMENT,
  `nameTag` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idTag`)
) ENGINE=MyISAM AUTO_INCREMENT=114 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES (1,'paysage'),(2,'portrait'),(3,'voyage'),(4,'nature'),(5,'architecture'),(6,'ville'),(7,'nourriture'),(8,'animal'),(9,'personnes'),(10,'action'),(11,'sport'),(12,'noir et blanc'),(13,'art'),(14,'mode'),(15,'technologie'),(16,'coucher de soleil'),(17,'selfie'),(18,'famille'),(19,'amis'),(20,'mariage'),(21,'bébé'),(22,'vacances'),(23,'abstrait'),(24,'macro'),(25,'amusant'),(26,'musique'),(27,'danse'),(28,'performance'),(29,'film'),(30,'théâtre'),(31,'automne'),(32,'hiver'),(33,'printemps'),(34,'été'),(35,'nuages'),(36,'ciel'),(37,'océan'),(38,'plage'),(39,'montagnes'),(40,'parc'),(41,'jardin'),(42,'véhicules'),(43,'vélos'),(44,'voitures'),(45,'bateaux'),(46,'avions'),(47,'trains'),(48,'lever de soleil'),(49,'nuit'),(50,'astro'),(51,'étoiles'),(52,'lune'),(53,'espace'),(54,'eau'),(103,'zen'),(56,'neige'),(57,'brouillard'),(58,'sourire'),(59,'rire'),(60,'triste'),(61,'heureux'),(62,'amour'),(63,'paix'),(64,'inspiration'),(65,'motivation'),(66,'positif'),(67,'beauté'),(68,'santé'),(69,'fitness'),(70,'yoga'),(71,'méditation'),(72,'relaxation'),(73,'bien-être'),(74,'spa'),(75,'massage'),(76,'cheveux'),(77,'maquillage'),(78,'ongles'),(79,'bijoux'),(80,'accessoires'),(81,'chaussures'),(82,'sac'),(83,'vêtements'),(85,'style'),(86,'tendance'),(87,'design'),(88,'intérieur'),(89,'décoration'),(90,'maison'),(91,'jardin'),(92,'meubles'),(93,'DIY'),(94,'fait main'),(95,'arts'),(96,'scrapbooking'),(97,'calligraphie'),(98,'peinture'),(99,'dessin');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tagforphoto`
--

DROP TABLE IF EXISTS `tagforphoto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagforphoto` (
  `idTag` int(11) NOT NULL,
  `idPhoto` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tagforphoto`
--

LOCK TABLES `tagforphoto` WRITE;
/*!40000 ALTER TABLE `tagforphoto` DISABLE KEYS */;
INSERT INTO `tagforphoto` VALUES (1,51),(39,51),(4,50),(73,52),(4,52),(4,51),(87,48),(5,48),(4,49),(8,50),(13,50),(4,54),(73,54),(85,54),(67,55),(73,55),(8,56),(61,57),(25,57),(19,57),(73,58),(21,58),(62,58),(67,59),(31,59),(86,59),(70,60),(13,60),(4,61),(4,62),(4,63),(73,63),(68,63),(8,66),(8,67),(4,68);
/*!40000 ALTER TABLE `tagforphoto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `surnameUser` varchar(45) DEFAULT NULL,
  `nameUser` varchar(45) DEFAULT NULL,
  `emailUser` varchar(45) DEFAULT NULL,
  `passwordUser` varchar(150) DEFAULT NULL,
  `rankUser` int(11) NOT NULL DEFAULT '0',
  `creditUser` decimal(9,2) NOT NULL DEFAULT '0.00',
  `reductionCreditUser` varchar(45) DEFAULT NULL,
  `isBanUser` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`idUser`),
  UNIQUE KEY `emailUser_UNIQUE` (`emailUser`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (17,'masia','antoine','test@test.fr','b68c0a4c5bd6ab1f693fcac1cc9d9ff802cefb7efce3d2a0800fd23e1e503cd2',1,8.14,NULL,1),(15,'roxitar','richar','test2@test.fr','b68c0a4c5bd6ab1f693fcac1cc9d9ff802cefb7efce3d2a0800fd23e1e503cd2',2,12.00,'0',0),(11,'despa','mathieu','test3@test.fr','c82fddd7000a1f5196b079abaf3b239623fc2ea3088c5dac8e45c8de4c813aa5',2,62.40,'5',0),(12,'dupont','romain','admin@admin.fr','c82fddd7000a1f5196b079abaf3b239623fc2ea3088c5dac8e45c8de4c813aa5',3,0.00,'5',0),(16,'jordias','matheo','test4@test.fr','b68c0a4c5bd6ab1f693fcac1cc9d9ff802cefb7efce3d2a0800fd23e1e503cd2',2,0.00,NULL,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-22 17:31:37
