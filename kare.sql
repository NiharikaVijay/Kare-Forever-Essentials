-- MariaDB dump 10.19  Distrib 10.4.18-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: kare
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

use kare;
--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `addid` varchar(10) NOT NULL,
  `cxid` varchar(10) NOT NULL,
  `line1` text DEFAULT NULL,
  `line2` text DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `pincode` int(6) NOT NULL,
  KEY `cxid` (`cxid`),
  CONSTRAINT `address_ibfk_1` FOREIGN KEY (`cxid`) REFERENCES `customer` (`cxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
INSERT INTO `address` VALUES ('add1','a001','Address Line 1','Address Line 2','Bangalore','Karnataka',560054);
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `admid` varchar(10) NOT NULL,
  `admname` varchar(30) DEFAULT NULL,
  `admemail` varchar(40) NOT NULL,
  `admphone` varchar(10) DEFAULT NULL,
  `admpass` varchar(25) NOT NULL,
  PRIMARY KEY (`admid`),
  UNIQUE KEY `admphone` (`admphone`,`admemail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `cxid` varchar(10) NOT NULL,
  `pdid` varchar(15) NOT NULL,
  `pdvolume` varchar(7) NOT NULL,
  `pdqty` int(3) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  KEY `cxid` (`cxid`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`cxid`) REFERENCES `customer` (`cxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES ('a001','prod1','500mL',5,'2021-05-13 12:28:39'),('a001','prod2','250mL',4,'2021-05-13 12:29:49'),('a002','prod2','250mL',7,'2021-05-13 12:30:03');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `catid` varchar(15) NOT NULL,
  `catname` varchar(30) NOT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES ('cat1','Nourishing creams'),('cat2','Face Cream Pack');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `concern`
--

DROP TABLE IF EXISTS `concern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `concern` (
  `concid` varchar(15) NOT NULL,
  `concname` text NOT NULL,
  PRIMARY KEY (`concid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `concern`
--

LOCK TABLES `concern` WRITE;
/*!40000 ALTER TABLE `concern` DISABLE KEYS */;
INSERT INTO `concern` VALUES ('con1','Dry Skin'),('con2','Dead cells');
/*!40000 ALTER TABLE `concern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `cxid` varchar(10) NOT NULL,
  `cxname` varchar(40) DEFAULT NULL,
  `cxphone` varchar(10) DEFAULT NULL,
  `cxemail` varchar(40) NOT NULL,
  PRIMARY KEY (`cxid`),
  UNIQUE KEY `cxemail` (`cxemail`),
  UNIQUE KEY `cxphone` (`cxphone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES ('a001','Amodh','9110837927','amodhshenoy@gmail.com'),('a002','Niharika','1234567890','niharika@gmail.com');
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `mdid` varchar(10) NOT NULL,
  `path` text DEFAULT NULL,
  `isimage` tinyint(1) DEFAULT NULL,
  `pdid` varchar(10) NOT NULL,
  PRIMARY KEY (`mdid`),
  KEY `fk_media_product` (`pdid`),
  CONSTRAINT `fk_media_product` FOREIGN KEY (`pdid`) REFERENCES `product` (`pdid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderproducts`
--

DROP TABLE IF EXISTS `orderproducts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderproducts` (
  `orderid` varchar(15) NOT NULL,
  `pdid` varchar(15) NOT NULL,
  `pdvolume` varchar(8) NOT NULL,
  `pdqty` int(3) NOT NULL,
  `pdprice` float DEFAULT NULL,
  KEY `pdid` (`pdid`),
  CONSTRAINT `orderproducts_ibfk_1` FOREIGN KEY (`pdid`) REFERENCES `product` (`pdid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderproducts`
--

LOCK TABLES `orderproducts` WRITE;
/*!40000 ALTER TABLE `orderproducts` DISABLE KEYS */;
INSERT INTO `orderproducts` VALUES ('ord1','prod1','100mL',2,NULL);
/*!40000 ALTER TABLE `orderproducts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `cxid` varchar(10) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `adid` varchar(10) NOT NULL,
  `amount` float DEFAULT NULL,
  `status` varchar(15) NOT NULL,
  `paymentid` varchar(20) DEFAULT NULL,
  `ordid` varchar(10) NOT NULL,
  `notes` text DEFAULT NULL,
  UNIQUE KEY `unique_order_id` (`ordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES ('a001','2021-05-13 17:47:48','add1',500,'Placed','abcdef123','ord1',NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp`
--

DROP TABLE IF EXISTS `otp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otp` (
  `cxid` varchar(10) NOT NULL,
  `OTP` int(11) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  KEY `cxid` (`cxid`),
  CONSTRAINT `otp_ibfk_1` FOREIGN KEY (`cxid`) REFERENCES `customer` (`cxid`),
  CONSTRAINT `otp_ibfk_2` FOREIGN KEY (`cxid`) REFERENCES `customer` (`cxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp`
--

LOCK TABLES `otp` WRITE;
/*!40000 ALTER TABLE `otp` DISABLE KEYS */;
/*!40000 ALTER TABLE `otp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price`
--

DROP TABLE IF EXISTS `price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `price` (
  `pdid` varchar(15) NOT NULL,
  `volume` varchar(7) NOT NULL,
  `price` float DEFAULT NULL,
  `discount` float DEFAULT NULL,
  KEY `pdid` (`pdid`),
  CONSTRAINT `price_ibfk_1` FOREIGN KEY (`pdid`) REFERENCES `product` (`pdid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price`
--

LOCK TABLES `price` WRITE;
/*!40000 ALTER TABLE `price` DISABLE KEYS */;
/*!40000 ALTER TABLE `price` ENABLE KEYS */;
UNLOCK TABLES;

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
  `catid` varchar(15) NOT NULL,
  `concid` varchar(15) NOT NULL,
  `isfeatured` tinyint(1) DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`pdid`),
  KEY `concid` (`concid`),
  KEY `catid` (`catid`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`concid`) REFERENCES `concern` (`concid`),
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`catid`) REFERENCES `category` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES ('prod1','Almond Cream - Shea butter, Vit A','Infused with Aqua, Beeswax,  Almond extract, Shea Butter, Gylcerine, Sweet Almond Oil, and Vitamin A.','Helps treat dry skin, rejuvenate skin, reduce scars and anti ageing.Helps in deep moisturising and supple soft skin.','After cleansing with water, apply the cream and massage gently for 2 - 5 mins.','cream, almond, shea, butter','cat1','con1',1,1),('prod2','Coffee & Honey Face Pack - Rosewood & Bergamot','Cream based pack made with milk and natural extracts of coffee and honey. Aqua, Kaoline, Rosewood Oil, Bergamot Oil, Bentonile, Grape seed extract, Xernthum Gum.','Antioxidants  remove dead cells, nourishes and moisturize skin and helps in exfoliating , moisturising  and deep cleansing.','Apply  the cream based pack on clean face for 20 mins . Wash with clean water.','cream, pack, coffee, honey','cat2','con2',1,1);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `revid` int(15) NOT NULL,
  `cxid` varchar(10) NOT NULL,
  `pdid` int(15) NOT NULL,
  `rating` int(5) NOT NULL,
  `message` text DEFAULT NULL,
  KEY `cxid` (`cxid`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`cxid`) REFERENCES `customer` (`cxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlist` (
  `cxid` varchar(10) NOT NULL,
  `pdid` varchar(15) NOT NULL,
  `pdvolume` varchar(8) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  KEY `cxid` (`cxid`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`cxid`) REFERENCES `customer` (`cxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-15 21:50:45
