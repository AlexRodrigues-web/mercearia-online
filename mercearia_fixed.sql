CREATE DATABASE  IF NOT EXISTS `mercearia` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `mercearia`;
-- MySQL dump 10.13  Distrib 8.0.25, for Win64 (x86_64)
--
-- Host: localhost    Database: mercearia
-- ------------------------------------------------------
-- Server version	8.0.25

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
-- Table structure for table `caixa`
--

DROP TABLE IF EXISTS `caixa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caixa` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `cliente` int NOT NULL,;
  `produto_id` int NOT NULL,;
  `quantidade` int NOT NULL,;
  `subTotal` double NOT NULL,;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`id`,`produto_id`,`cliente`),;
  KEY `fk_caixa_produto_idx` (`produto_id`) /*!80000 INVISIBLE */,;
  KEY `cliente_idx` (`cliente`),;
  CONSTRAINT `fk_caixa_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa`
--

LOCK TABLES `caixa` WRITE;
/*!40000 ALTER TABLE `caixa` DISABLE KEYS */;
INSERT INTO `caixa` VALUES (1,1,4,5,125,'2021-09-25 23:13:09'),(2,1,8,5,150,'2021-09-25 23:13:09'),(3,1,9,1,25,'2021-09-25 23:13:09'),(4,1,19,2,5.4,'2021-09-25 23:13:09'),(5,2,4,5,125,'2021-09-25 23:15:48'),(6,2,8,5,150,'2021-09-25 23:15:48'),(7,2,9,1,25,'2021-09-25 23:15:48'),(8,2,19,2,5.4,'2021-09-25 23:15:48'),(9,3,6,2,10,'2021-09-26 00:09:14'),(10,3,18,2,7,'2021-09-26 00:09:14'),(11,3,5,1,7,'2021-09-26 00:09:14'),(12,3,12,1,39.5,'2021-09-26 00:09:14'),(13,3,2,1,5.5,'2021-09-26 00:09:14'),(14,3,17,3,20.55,'2021-09-26 00:09:14'),(16,4,8,1,30,'2021-09-27 15:42:39'),(17,4,19,1,2.7,'2021-09-27 15:42:39'),(18,5,19,20,54,'2021-09-27 16:10:02'),(19,6,8,1,30,'2021-09-29 19:03:48'),(20,7,19,1,2.7,'2021-09-29 19:04:55'),(21,8,19,2,5.4,'2021-09-29 19:05:27'),(22,8,20,1,1,'2021-09-29 19:05:27'),(23,9,19,1,2.7,'2021-09-29 20:01:16'),(24,10,20,1,1,'2021-09-29 20:02:20'),(25,11,4,2,50,'2021-09-30 12:31:49'),(26,11,8,2,60,'2021-09-30 12:31:49'),(27,11,9,2,50,'2021-09-30 12:31:49'),(28,11,30,6,108,'2021-09-30 12:31:49');
/*!40000 ALTER TABLE `caixa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caixa_fechamento`
--

DROP TABLE IF EXISTS `caixa_fechamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caixa_fechamento` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `cliente_id` int NOT NULL,;
  `total` double NOT NULL,;
  `pagamento` varchar(9) COLLATE utf8mb4_general_ci NOT NULL,;
  `valor` double NOT NULL,;
  `troco` double NOT NULL,;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`id`,`cliente_id`),;
  KEY `fk_caixa_fechamento_caixa_idx` (`cliente_id`),;
  CONSTRAINT `fk_caixa_fechamento_caixa` FOREIGN KEY (`cliente_id`) REFERENCES `caixa` (`cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa_fechamento`
--

LOCK TABLES `caixa_fechamento` WRITE;
/*!40000 ALTER TABLE `caixa_fechamento` DISABLE KEYS */;
INSERT INTO `caixa_fechamento` VALUES (1,1,305.4,'Dinheiro',320,14.6,'2021-09-25 23:13:09'),(2,2,305.4,'Dinheiro',320,14.6,'2021-09-25 23:15:48'),(3,3,89.55,'Débito',89.55,0,'2021-09-26 00:09:14'),(4,4,32.7,'Dinheiro',100,67.3,'2021-09-27 15:42:39'),(5,5,54,'Dinheiro',60,6,'2021-09-27 16:10:02'),(6,6,30,'Dinheiro',50,20,'2021-09-29 19:03:48'),(7,7,2.7,'Dinheiro',5,2.3,'2021-09-29 19:04:55'),(8,8,6.4,'Dinheiro',10,3.6,'2021-09-29 19:05:27'),(9,9,2.7,'Crédito',2.7,0,'2021-09-29 20:01:16'),(10,10,1,'Dinheiro',2,1,'2021-09-29 20:02:20'),(11,11,268,'Dinheiro',300,32,'2021-09-30 12:31:49');
/*!40000 ALTER TABLE `caixa_fechamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargo` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `nome` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  PRIMARY KEY (`id`);
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo`
--

LOCK TABLES `cargo` WRITE;
/*!40000 ALTER TABLE `cargo` DISABLE KEYS */;
INSERT INTO `cargo` VALUES (1,'Administrador'),(2,'Operador de caixa'),(3,'Repositor');
/*!40000 ALTER TABLE `cargo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `codigo`
--

DROP TABLE IF EXISTS `codigo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codigo` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `barra` varchar(43) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1234567890',;
  PRIMARY KEY (`id`),;
  UNIQUE KEY `barra_UNIQUE` (`barra`);
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `codigo`
--

LOCK TABLES `codigo` WRITE;
/*!40000 ALTER TABLE `codigo` DISABLE KEYS */;
INSERT INTO `codigo` VALUES (1,'1234567890'),(2,'1234567891'),(3,'1234567892'),(4,'1234567893'),(5,'1234567894'),(6,'1234567895'),(7,'1234567896'),(8,'1234567897'),(9,'1234567898'),(10,'1234567899'),(11,'1234567900'),(12,'1234567901'),(13,'1234567902'),(14,'1234567903'),(15,'1234567904'),(16,'1234567905'),(17,'1234567906'),(18,'1234567907'),(19,'1234567908'),(20,'1234567909'),(21,'1234567910'),(22,'1234567911'),(23,'1234567912'),(24,'1234567913'),(25,'1234567914'),(26,'1234567915'),(27,'1234567916'),(28,'1234567917'),(29,'1234567918'),(30,'1234567919'),(31,'1234567920');
/*!40000 ALTER TABLE `codigo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estoque` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `produto_id` int NOT NULL,;
  `quantidade` int NOT NULL,;
  PRIMARY KEY (`id`,`produto_id`),;
  UNIQUE KEY `produto_id_UNIQUE` (`produto_id`),;
  KEY `fk_estoque_produto_idx` (`produto_id`) /*!80000 INVISIBLE */,;
  CONSTRAINT `fk_estoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
INSERT INTO `estoque` VALUES (1,1,900),(2,2,899),(3,3,900),(4,4,888),(5,5,899),(6,6,898),(7,7,900),(8,8,886),(9,9,896),(10,10,900),(11,11,900),(12,12,899),(13,13,900),(14,14,900),(15,15,900),(16,16,900),(17,17,897),(18,18,898),(19,19,871),(20,20,898),(21,21,900),(22,22,900),(23,23,900),(24,24,900),(25,25,900),(26,26,900),(27,27,900),(28,28,900),(29,29,900),(30,30,894),(31,31,900);
/*!40000 ALTER TABLE `estoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fornecedor` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `nome` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `cnpj` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`id`),;
  UNIQUE KEY `cnpj_UNIQUE` (`cnpj`);
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedor`
--

LOCK TABLES `fornecedor` WRITE;
/*!40000 ALTER TABLE `fornecedor` DISABLE KEYS */;
INSERT INTO `fornecedor` VALUES (1,'Marques Monte','12.123.456/0101-02','2021-07-29 08:10:30'),(2,'Panificadora Tomé','10.241.584/2014-54','2021-07-29 08:10:30'),(3,'Heinz','50.955.707/0001-20','2021-08-11 23:45:27'),(4,' Visconti','70.940.994/0040-18','2021-08-31 20:51:03'),(5,'Coca Cola Industriais LTDA','45.997.418/0001-53','2021-09-04 16:18:19');
/*!40000 ALTER TABLE `fornecedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionario`
--

DROP TABLE IF EXISTS `funcionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionario` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `nome` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `cargo_id` int NOT NULL,;
  `nivel_id` int NOT NULL,;
  `credencial` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `senha` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `ativo` tinyint(1) NOT NULL DEFAULT '1',;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`id`,`cargo_id`,`nivel_id`),;
  UNIQUE KEY `credencial_UNIQUE` (`credencial`),;
  KEY `fk_funcionario_cargo_idx` (`cargo_id`) /*!80000 INVISIBLE */,;
  KEY `fk_funcionario_nivel_idx` (`nivel_id`),;
  CONSTRAINT `fk_funcionario_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargo` (`id`),;
  CONSTRAINT `fk_funcionario_nivel` FOREIGN KEY (`nivel_id`) REFERENCES `nivel` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario`
--

LOCK TABLES `funcionario` WRITE;
/*!40000 ALTER TABLE `funcionario` DISABLE KEYS */;
INSERT INTO `funcionario` VALUES (1,'Lucas Akio Turuda',1,3,'lucasakio','$2y$10$IqFJS0D8S0Ddq3PQk7TIsOCsEcXBiEKBda5fsBIMp57sCvCaLUlvG',1,'2021-09-03 23:46:27'),(2,'José Gomes Pereira',2,3,'josegomes','$2y$10$ADACziYi8E4kZQZNastgBeOYy6ALa9u0sUNjaPWumkx5Y.s9/pA6S',0,'2021-09-11 17:37:17'),(3,'Marcos Santos',2,1,'marcossantos','$2y$10$zNBZ1SjXUptv6HEnDZtkpOtNRjAC7RKRdtU/WCAkeKHVJ6dDR9AH.',1,'2021-09-29 18:23:12');
/*!40000 ALTER TABLE `funcionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionario_pg_privada`
--

DROP TABLE IF EXISTS `funcionario_pg_privada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionario_pg_privada` (;
  `funcionario_id` int NOT NULL,;
  `pg_privada_id` int NOT NULL,;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`funcionario_id`,`pg_privada_id`),;
  KEY `fk_funcionario_pg_privada_pg_privada_idx` (`pg_privada_id`),;
  KEY `fk_funcionario_pg_privada_funcionario_idx` (`funcionario_id`) /*!80000 INVISIBLE */,;
  CONSTRAINT `fk_funcionario_pg_privada_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,;
  CONSTRAINT `fk_funcionario_pg_privada_pg_privada` FOREIGN KEY (`pg_privada_id`) REFERENCES `pg_privada` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario_pg_privada`
--

LOCK TABLES `funcionario_pg_privada` WRITE;
/*!40000 ALTER TABLE `funcionario_pg_privada` DISABLE KEYS */;
INSERT INTO `funcionario_pg_privada` VALUES (1,1,'2021-09-04 18:50:12'),(1,2,'2021-09-04 18:50:12'),(1,3,'2021-09-04 18:50:12'),(1,4,'2021-09-04 18:50:12'),(1,5,'2021-09-04 18:50:12'),(1,6,'2021-09-04 18:50:12'),(1,7,'2021-09-04 18:50:12'),(1,8,'2021-09-04 18:50:12'),(1,9,'2021-09-04 18:50:12'),(1,10,'2021-09-04 18:50:12'),(1,11,'2021-09-04 18:50:12'),(2,1,'2021-09-11 17:37:17'),(2,3,'2021-09-11 17:37:17'),(2,5,'2021-09-11 17:37:17'),(2,11,'2021-09-11 17:37:17'),(3,1,'2021-09-29 18:23:12'),(3,3,'2021-09-29 18:23:12'),(3,11,'2021-09-29 18:23:12');
/*!40000 ALTER TABLE `funcionario_pg_privada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nivel`
--

DROP TABLE IF EXISTS `nivel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nivel` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `nome` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  PRIMARY KEY (`id`),;
  UNIQUE KEY `nome_UNIQUE` (`nome`);
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel`
--

LOCK TABLES `nivel` WRITE;
/*!40000 ALTER TABLE `nivel` DISABLE KEYS */;
INSERT INTO `nivel` VALUES (1,'Inicial'),(2,'Parcial'),(3,'Total');
/*!40000 ALTER TABLE `nivel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pg_privada`
--

DROP TABLE IF EXISTS `pg_privada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pg_privada` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `nome` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`id`),;
  UNIQUE KEY `nome_UNIQUE` (`nome`);
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pg_privada`
--

LOCK TABLES `pg_privada` WRITE;
/*!40000 ALTER TABLE `pg_privada` DISABLE KEYS */;
INSERT INTO `pg_privada` VALUES (1,'Home','2021-08-09 19:55:47'),(2,'Produtos','2021-08-09 19:55:47'),(3,'Perfil','2021-08-09 19:55:47'),(4,'Fornecedor','2021-08-31 18:14:59'),(5,'Estoque','2021-09-01 16:05:55'),(6,'Cargo','2021-09-01 16:05:55'),(7,'Nivel','2021-09-01 18:39:38'),(8,'Pagina_privada','2021-09-01 18:55:45'),(9,'Pagina_publica','2021-09-01 18:55:45'),(10,'Funcionario','2021-09-01 19:05:43'),(11,'Caixa','2021-09-04 16:19:38');
/*!40000 ALTER TABLE `pg_privada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pg_publica`
--

DROP TABLE IF EXISTS `pg_publica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pg_publica` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `nome` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`id`),;
  UNIQUE KEY `nome_UNIQUE` (`nome`);
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pg_publica`
--

LOCK TABLES `pg_publica` WRITE;
/*!40000 ALTER TABLE `pg_publica` DISABLE KEYS */;
INSERT INTO `pg_publica` VALUES (1,'Login','2021-09-04 16:20:03'),(2,'PaginaInvalida','2021-09-02 09:41:26'),(3,'Sair','2021-09-02 09:47:08');
/*!40000 ALTER TABLE `pg_publica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto` (;
  `id` int NOT NULL AUTO_INCREMENT,;
  `nome` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,;
  `preco` double NOT NULL,;
  `fornecedor_id` int NOT NULL,;
  `codigo_id` int NOT NULL,;
  `kilograma` double NOT NULL DEFAULT '0',;
  `litro` double NOT NULL DEFAULT '0',;
  `dt_registro` datetime NOT NULL,;
  PRIMARY KEY (`id`,`fornecedor_id`,`codigo_id`),;
  KEY `fk_produto_fornecedor_idx` (`fornecedor_id`),;
  KEY `fk_produto_codigo_idx` (`codigo_id`),;
  CONSTRAINT `fk_produto_codigo` FOREIGN KEY (`codigo_id`) REFERENCES `codigo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,;
  CONSTRAINT `fk_produto_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedor` (`id`);
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'Pão integral',7.49,2,1,0.5,0,'2021-08-25 23:35:39'),(2,'Pão de forma',5.5,2,2,0.5,0,'2021-07-29 08:27:37'),(3,'Queijo prato',46,1,3,1.25,0,'2021-09-30 09:38:18'),(4,'Bolo de limão',25,2,4,1,0,'2021-08-11 21:24:17'),(5,'Molho Barbecue Heinz',7,3,5,0.397,0,'2021-08-12 00:05:05'),(6,'Requeijão',5,2,6,1,0,'2021-08-12 00:03:57'),(7,'Goiabada',4.5,2,7,0.5,0,'2021-08-12 00:04:50'),(8,'Bolo de maracujá',35,2,8,1,0,'2021-09-30 13:13:32'),(9,'Bolo de chocolate',25,2,9,1,0,'2021-08-12 00:05:04'),(10,'Bolo de cenoura',20,2,10,1,0,'2021-08-12 00:05:04'),(11,'Bolo de abacaxi',20,2,11,1.5,0,'2021-08-26 20:19:32'),(12,'Presunto Marques Monte',39.5,1,12,1,0,'2021-08-12 00:05:04'),(13,'Molho de tomate Heinz',4,3,13,0.4,0,'2021-08-12 00:05:04'),(14,'Catchup Heinz',5.5,3,14,0.26,0,'2021-08-12 00:05:05'),(15,'Maionese Sachê Heinz',0.5,3,15,0.8,0,'2021-08-12 00:05:05'),(16,'Maionese Tradicional Heinz',6.7,3,16,0.215,0,'2021-08-12 00:05:05'),(17,'Leite condensado Moça',6.85,2,17,0.395,0,'2021-08-28 11:09:01'),(18,'Creme de leite nestlé',3.5,1,18,0.25,0,'2021-08-28 16:06:53'),(19,'Biscoito de morango Trakinas ',2.7,1,19,0.13,0,'2021-09-11 16:26:09'),(20,'Fermento biológico Dona Benta',1,2,20,0.05,0,'2021-09-11 16:37:31'),(21,'Coca cola lata',3,5,21,0,0.35,'2021-09-27 17:25:01'),(22,'Coca cola zero lata',3,5,22,0,0.35,'2021-09-27 17:25:15'),(23,'Água de coco',6.5,1,23,0,1,'2021-09-27 17:17:29'),(24,'Refrigerante Coca cola 600 ml',5,5,24,0,0.6,'2021-09-27 17:27:10'),(25,'Refrigerante Coca cola 1 L',7.5,5,25,0,1,'2021-09-27 17:35:25'),(26,'Refrigerante Coca cola 2 L',10,5,26,0,2,'2021-09-27 17:29:12'),(27,'Refrigerante Coca zero 1 L',7.5,5,27,0,1,'2021-09-27 17:35:03'),(28,'Refrigerante Coca zero 2 L',10,5,28,0,2,'2021-09-27 17:31:23'),(29,'Refrigerante Coca cola 2,5 L',15,5,29,0,2.5,'2021-09-27 17:33:33'),(30,'Refrigerante Coca cola 3 L',18,5,30,0,3,'2021-09-29 22:19:05'),(31,'Pote de açai',15,1,31,0,1,'2021-09-30 13:21:28');
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'mercearia'
--

--
-- Dumping routines for database 'mercearia'
--
/*!50003 DROP PROCEDURE IF EXISTS `atualizar_produto_estoque` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `atualizar_produto_estoque`(;
id int,;
nome varchar(30),;
preco double ,;
fornecedor_id int,;
codigo_id int,;
kilograma double,;
litro double,;
quantidade int;
);
BEGIN;
DECLARE erro_sql TINYINT DEFAULT FALSE;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET erro_sql = TRUE;

IF(id >= 1 and nome <> "" and preco >= 0.10 and fornecedor_id >=1 and kilograma >= 0 and litro >= 0 and codigo_id >= 1 and quantidade >= 0) THEN;

	START TRANSACTION;
	UPDATE produto AS p SET p.nome = nome, p.preco = preco, p.fornecedor_id = fornecedor_id, p.kilograma = kilograma, p.litro = litro, p.codigo_id = codigo_id, p.dt_registro = now() WHERE p.id = id;
	IF erro_sql = true;
		THEN;
		SELECT 'Erro ao atualizar na tabela produto' AS Mensagem;
	ROLLBACK;
    
	ELSE;
		UPDATE estoque AS e SET e.quantidade = quantidade WHERE e.id = id and e.produto_id = id;
		IF erro_sql = true;
			THEN;
			SELECT 'Erro ao atualizar no estoque' AS Mensagem;
		ROLLBACK;
		ELSE;
			 SELECT 'Produto atualizado com sucesso' AS Mensagem;
			  COMMIT;
		END IF;
	END IF;
ELSE;
	SELECT 'Preencha todos os campos e tente novamente!' AS Mensagem;
  END IF;              
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `cadastrar_codigo_produto_estoque` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `cadastrar_codigo_produto_estoque`(;
nome varchar(30),;
preco double ,;
fornecedor_id int,;
kilograma double,;
litro double,;
quantidade int;
);
BEGIN;
DECLARE erro_sql TINYINT DEFAULT FALSE;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET erro_sql = TRUE;

IF(nome <> "" and preco >= 0.10 and fornecedor_id >= 1 and kilograma >= 0 and litro >= 0 and quantidade >= 0) THEN;

	START TRANSACTION;
	SET @codigo = (SELECT barra + 1 AS valor FROM codigo ORDER BY id DESC LIMIT 1);
	INSERT INTO codigo values(NULL,ifnull(@codigo,default(barra)));
	IF erro_sql = true;
		THEN;
		SELECT 'Erro ao inserir na tabela de código' AS Mensagem;
	ROLLBACK;
	ELSE;
		INSERT INTO produto values(NULL, nome, preco, fornecedor_id, last_insert_id(), kilograma,litro, now());
		IF erro_sql = true;
			THEN;
			SELECT 'Erro ao inserir na tabela de produto' AS Mensagem;
		ROLLBACK;
		ELSE;
			INSERT INTO estoque values(null,last_insert_id(),quantidade);
			IF erro_sql = true;
				THEN;
				SELECT 'Erro ao inserir na tabela de estoque' AS Mensagem;
			ROLLBACK;
			ELSE;
			 SELECT 'Cadastro efetuado com sucesso' AS Mensagem;
			  COMMIT;
			END IF;
		END IF;
	END IF;
ELSE;
	SELECT 'Preencha todos os campos e tente novamente!' AS Mensagem;
END IF;
                
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-30 14:19:38

-- Creating missing table: cargo
CREATE TABLE `cargo` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(255) NOT NULL,
        `dt_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Creating missing table: nivel
CREATE TABLE `nivel` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(255) NOT NULL,
        `dt_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Creating missing table: pg_privada
CREATE TABLE `pg_privada` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(255) NOT NULL,
        `dt_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Creating missing table: pg_publica
CREATE TABLE `pg_publica` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(255) NOT NULL,
        `dt_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
