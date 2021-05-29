-- MariaDB dump 10.19  Distrib 10.4.18-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: kare2
-- ------------------------------------------------------
-- Server version	10.4.18-MariaDB

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
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `pdid` varchar(15) NOT NULL,
  `pdname` varchar(100) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `application` text NOT NULL,
  `tags` text DEFAULT NULL,
  `isfeatured` tinyint(1) DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`pdid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES ('prod1','Almond Cream - Shea butter, Vit A','Infused with Aqua, Beeswax,  Almond extract, Shea Butter, Gylcerine, Sweet Almond Oil, and Vitamin A.','Helps treat dry skin, rejuvenate skin, reduce scars and anti ageing.Helps in deep moisturising and supple soft skin.','After cleansing with water, apply the cream and massage gently for 2 - 5 mins.','cream, almond, shea, butter',1,1),('prod2','Coffee & Honey Face Pack - Rosewood & Bergamot','Cream based pack made with milk and natural extracts of coffee and honey. Aqua, Kaoline, Rosewood Oil, Bergamot Oil, Bentonile, Grape seed extract, Xernthum Gum.','Antioxidants  remove dead cells, nourishes and moisturize skin and helps in exfoliating , moisturising  and deep cleansing.','Apply  the cream based pack on clean face for 20 mins . Wash with clean water.','cream, pack, coffee, honey',1,1);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prodcat`
--

DROP TABLE IF EXISTS `prodcat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prodcat` (
  `pdid` varchar(10) DEFAULT NULL,
  `catid` varchar(10) DEFAULT NULL,
  KEY `fk_prodcat_product` (`pdid`),
  KEY `fk_prodcat_category` (`catid`),
  CONSTRAINT `fk_prodcat_category` FOREIGN KEY (`catid`) REFERENCES `category` (`catid`) ON DELETE CASCADE,
  CONSTRAINT `fk_prodcat_product` FOREIGN KEY (`pdid`) REFERENCES `product` (`pdid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodcat`
--

LOCK TABLES `prodcat` WRITE;
/*!40000 ALTER TABLE `prodcat` DISABLE KEYS */;
INSERT INTO `prodcat` VALUES ('prod1','cat1'),('prod2','cat2');
/*!40000 ALTER TABLE `prodcat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prodconc`
--

DROP TABLE IF EXISTS `prodconc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prodconc` (
  `pdid` varchar(10) DEFAULT NULL,
  `concid` varchar(10) DEFAULT NULL,
  KEY `fk_prodconc_product` (`pdid`),
  KEY `fk_prodconc_concern` (`concid`),
  CONSTRAINT `fk_prodconc_concern` FOREIGN KEY (`concid`) REFERENCES `concern` (`concid`) ON DELETE CASCADE,
  CONSTRAINT `fk_prodconc_product` FOREIGN KEY (`pdid`) REFERENCES `product` (`pdid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodconc`
--

LOCK TABLES `prodconc` WRITE;
/*!40000 ALTER TABLE `prodconc` DISABLE KEYS */;
INSERT INTO `prodconc` VALUES ('prod1','con1'),('prod1','con2'),('prod2','con2');
/*!40000 ALTER TABLE `prodconc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `revid` varchar(10) DEFAULT NULL,
  `cxid` varchar(10) NOT NULL,
  `pdid` varchar(10) DEFAULT NULL,
  `rating` int(5) NOT NULL,
  `message` text DEFAULT NULL,
  UNIQUE KEY `revid` (`revid`),
  KEY `cxid` (`cxid`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`cxid`) REFERENCES `customer` (`cxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES ('r001','a001','prod1',5,'Worked wonders for me. Definitely recommnded to others'),('r002','a002','prod1',3,'Good product'),('r003','a001','prod2',4,'Nice aroma, helped cure my dry skin'),('r004','a002','prod2',3,'Did not like texture');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-16 12:21:38
