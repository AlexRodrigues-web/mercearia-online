-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: mercearia
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
-- Table structure for table `caixa`
--

DROP TABLE IF EXISTS `caixa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caixa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `subTotal` double NOT NULL,
  `dt_registro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente` (`cliente`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `caixa_ibfk_1` FOREIGN KEY (`cliente`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `caixa_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa`
--

LOCK TABLES `caixa` WRITE;
/*!40000 ALTER TABLE `caixa` DISABLE KEYS */;
/*!40000 ALTER TABLE `caixa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caixa_fechamento`
--

DROP TABLE IF EXISTS `caixa_fechamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caixa_fechamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente` int(11) NOT NULL,
  `total` double NOT NULL,
  `pagamento` varchar(9) NOT NULL,
  `valor` double NOT NULL,
  `troco` double NOT NULL,
  `dt_registro` datetime NOT NULL,
  PRIMARY KEY (`id`,`cliente`),
  KEY `cliente` (`cliente`),
  CONSTRAINT `caixa_fechamento_ibfk_1` FOREIGN KEY (`cliente`) REFERENCES `caixa` (`cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa_fechamento`
--

LOCK TABLES `caixa_fechamento` WRITE;
/*!40000 ALTER TABLE `caixa_fechamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `caixa_fechamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
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
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `codigo`
--

DROP TABLE IF EXISTS `codigo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `codigo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barra` varchar(43) NOT NULL DEFAULT '1234567890',
  PRIMARY KEY (`id`),
  UNIQUE KEY `barra_UNIQUE` (`barra`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `codigo`
--

LOCK TABLES `codigo` WRITE;
/*!40000 ALTER TABLE `codigo` DISABLE KEYS */;
INSERT INTO `codigo` VALUES (36,'7891000000011'),(37,'7891000000028'),(38,'7891000000035'),(39,'7891000000042'),(40,'7891000000059'),(41,'7891000000066'),(42,'7891000000073'),(43,'7891000000080'),(44,'7891000000097'),(45,'7891000000103');
/*!40000 ALTER TABLE `codigo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estoque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produto_id` (`produto_id`),
  CONSTRAINT `estoque_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
/*!40000 ALTER TABLE `estoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `nipc` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `dt_registro` datetime NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nipc` (`nipc`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedor`
--

LOCK TABLES `fornecedor` WRITE;
/*!40000 ALTER TABLE `fornecedor` DISABLE KEYS */;
INSERT INTO `fornecedor` VALUES (1,'SuperAlimentar SA','123456789','alexrroliver200@gmail.com','932121766','Rua de B&amp;eacute;lgica 2450','2025-04-11 23:58:05',1),(2,'Distribuidora Atl&acirc;ntico','987654321','alexrroliver200@gmail.com','932121766','Rua de B&amp;eacute;lgica 2450','2025-04-11 23:58:05',NULL),(3,'Alimentos Bom Sabor Ltda','1122334455','alexrroliver200@gmail.com','227819590','Rua de B&amp;eacute;lgica 2450','2025-04-11 23:58:05',NULL),(4,'Grupo NutriMais','BR99887766554','','','','2025-04-11 23:58:05',1),(5,'Frutas Tropicais BR','BR11122233344','','','','2025-04-11 23:58:05',1),(6,'Produtos Silva & Cia','PT223344556','','','','2025-04-11 23:58:05',1),(7,'Sabores do Campo','BR55667788990','','','','2025-04-11 23:58:05',1),(8,'Mundo das Bebidas','PT445566778','','','','2025-04-11 23:58:05',1),(9,'Emp&oacute;rio do Sabor','7788990011','alexrroliver200@gmail.com','932121766','Rua de B&eacute;lgica 2450','2025-04-11 23:58:05',NULL),(10,'Lusitana Foods','PT667788990','','','','2025-04-11 23:58:05',1),(11,'Brasil Alimentos LTDA','987654345','alexrroliver200@gmail.com','227819590','Rua da B&eacute;lgica 2450','2025-05-05 23:31:28',NULL);
/*!40000 ALTER TABLE `fornecedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionario`
--

DROP TABLE IF EXISTS `funcionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `funcionario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(70) NOT NULL,
  `cargo_id` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL,
  `credencial` varchar(20) NOT NULL,
  `senha` varchar(256) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `dt_registro` datetime NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `nivel` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credencial` (`credencial`),
  KEY `cargo_id` (`cargo_id`),
  KEY `nivel_id` (`nivel_id`),
  CONSTRAINT `funcionario_ibfk_1` FOREIGN KEY (`cargo_id`) REFERENCES `cargo` (`id`),
  CONSTRAINT `funcionario_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `nivel` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario`
--

LOCK TABLES `funcionario` WRITE;
/*!40000 ALTER TABLE `funcionario` DISABLE KEYS */;
INSERT INTO `funcionario` VALUES (1,'Alex Oliveira',1,3,'alexoliver','$2y$10$IqFJS0D8S0Ddq3PQk7TIsOCsEcXBiEKBda5fsBIMp57sCvCaLUlvG',1,'2025-01-22 17:05:37',NULL,NULL),(41,'Funcionario1',3,1,'funcionario1@teste.c',NULL,1,'2025-05-05 17:32:44',NULL,NULL);
/*!40000 ALTER TABLE `funcionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionario_pg_privada`
--

DROP TABLE IF EXISTS `funcionario_pg_privada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `funcionario_pg_privada` (
  `funcionario_id` int(11) NOT NULL,
  `pg_privada_id` int(11) NOT NULL,
  `dt_registro` datetime NOT NULL,
  PRIMARY KEY (`funcionario_id`,`pg_privada_id`),
  KEY `fk_funcionario_pg_privada_pg_privada` (`pg_privada_id`),
  CONSTRAINT `fk_funcionario_pg_privada_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_funcionario_pg_privada_pg_privada` FOREIGN KEY (`pg_privada_id`) REFERENCES `pg_privada` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario_pg_privada`
--

LOCK TABLES `funcionario_pg_privada` WRITE;
/*!40000 ALTER TABLE `funcionario_pg_privada` DISABLE KEYS */;
/*!40000 ALTER TABLE `funcionario_pg_privada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `niveis`
--

DROP TABLE IF EXISTS `niveis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `niveis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `niveis`
--

LOCK TABLES `niveis` WRITE;
/*!40000 ALTER TABLE `niveis` DISABLE KEYS */;
/*!40000 ALTER TABLE `niveis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nivel`
--

DROP TABLE IF EXISTS `nivel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nivel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(7) NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel`
--

LOCK TABLES `nivel` WRITE;
/*!40000 ALTER TABLE `nivel` DISABLE KEYS */;
INSERT INTO `nivel` VALUES (1,'Inicial','ativo'),(2,'Parcial','ativo'),(3,'Total','ativo'),(4,'TESTE','ativo');
/*!40000 ALTER TABLE `nivel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nivel_paginas`
--

DROP TABLE IF EXISTS `nivel_paginas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nivel_paginas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nivel_id` int(11) DEFAULT NULL,
  `pagina` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nivel_id` (`nivel_id`),
  CONSTRAINT `nivel_paginas_ibfk_1` FOREIGN KEY (`nivel_id`) REFERENCES `niveis` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel_paginas`
--

LOCK TABLES `nivel_paginas` WRITE;
/*!40000 ALTER TABLE `nivel_paginas` DISABLE KEYS */;
/*!40000 ALTER TABLE `nivel_paginas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `metodo_pagamento` varchar(50) NOT NULL,
  `data_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,19,3.87,'dinheiro','2025-04-19 02:49:52'),(2,19,2.16,'dinheiro','2025-04-19 02:55:29'),(3,19,2.16,'dinheiro','2025-04-19 02:56:08'),(4,19,4.38,'dinheiro','2025-04-19 02:59:58'),(5,19,2.58,'paypal','2025-04-19 03:00:24'),(6,19,2.16,'dinheiro','2025-04-19 03:07:28'),(7,19,4.43,'dinheiro','2025-04-19 12:34:04'),(8,19,17.44,'dinheiro','2025-04-21 17:27:38'),(9,19,2.16,'dinheiro','2025-04-22 17:21:35'),(10,19,14.80,'dinheiro','2025-04-25 12:13:43'),(11,19,37.85,'mbway','2025-05-02 00:52:01'),(12,19,16.33,'cartao','2025-05-02 00:53:02'),(13,19,0.83,'dinheiro','2025-05-07 00:52:07'),(14,19,1.99,'dinheiro','2025-05-07 19:14:38'),(15,19,3.98,'dinheiro','2025-05-07 19:21:14');
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_itens`
--

DROP TABLE IF EXISTS `pedido_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`),
  CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_itens`
--

LOCK TABLES `pedido_itens` WRITE;
/*!40000 ALTER TABLE `pedido_itens` DISABLE KEYS */;
INSERT INTO `pedido_itens` VALUES (1,1,44,1,3.87),(2,2,13,1,2.16),(3,3,13,1,2.16),(4,4,45,1,4.38),(5,5,48,1,2.58),(6,6,13,1,2.16),(7,7,2,1,4.43),(8,8,43,1,2.97),(9,8,34,1,3.70),(10,8,48,2,2.58),(11,8,5,3,1.87),(12,9,13,1,2.16),(13,10,34,4,3.70),(14,11,6,1,3.83),(15,11,45,1,4.38),(16,11,47,1,3.54),(17,11,34,1,3.70),(18,11,43,1,2.97),(19,11,4,1,4.23),(20,11,5,1,1.87),(21,11,42,1,1.52),(22,11,8,1,3.76),(23,11,33,1,3.88),(24,11,28,1,4.17),(25,12,46,1,3.32),(26,12,13,1,2.16),(27,12,39,1,2.00),(28,12,2,1,4.43),(29,12,17,1,4.42),(30,13,6,1,0.83),(31,14,50,1,1.99),(32,15,50,2,1.99);
/*!40000 ALTER TABLE `pedido_itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pg_privada`
--

DROP TABLE IF EXISTS `pg_privada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pg_privada` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) NOT NULL,
  `dt_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_UNIQUE` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pg_privada`
--

LOCK TABLES `pg_privada` WRITE;
/*!40000 ALTER TABLE `pg_privada` DISABLE KEYS */;
/*!40000 ALTER TABLE `pg_privada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pg_publica`
--

DROP TABLE IF EXISTS `pg_publica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pg_publica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) NOT NULL,
  `dt_registro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_UNIQUE` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pg_publica`
--

LOCK TABLES `pg_publica` WRITE;
/*!40000 ALTER TABLE `pg_publica` DISABLE KEYS */;
/*!40000 ALTER TABLE `pg_publica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phinxlog`
--

DROP TABLE IF EXISTS `phinxlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phinxlog`
--

LOCK TABLES `phinxlog` WRITE;
/*!40000 ALTER TABLE `phinxlog` DISABLE KEYS */;
INSERT INTO `phinxlog` VALUES (20250304230001,'CreateFuncionariosTable','2025-03-09 00:29:38','2025-03-09 00:29:38',0),(20250304230002,'CreateFornecedoresTable','2025-03-09 00:29:38','2025-03-09 00:29:38',0),(20250304230003,'CreateProdutosTable','2025-03-09 00:29:38','2025-03-09 00:29:38',0),(20250306213346,'AdicionarTabelasFaltantes','2025-03-09 00:29:38','2025-03-09 00:29:38',0);
/*!40000 ALTER TABLE `phinxlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` double NOT NULL,
  `fornecedor_id` int(11) NOT NULL,
  `kilograma` double NOT NULL DEFAULT 0,
  `litro` double NOT NULL DEFAULT 0,
  `codigo_id` int(11) NOT NULL,
  `dt_registro` datetime NOT NULL,
  `categoria` varchar(50) NOT NULL DEFAULT 'Outros',
  `imagem` varchar(255) DEFAULT NULL,
  `estoque` int(11) DEFAULT 100,
  `estoque_minimo` int(11) NOT NULL DEFAULT 10,
  `unidade` varchar(20) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL,
  `local` varchar(100) DEFAULT NULL,
  `nipc` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'Acucar Branco 1kg','A??car refinado de alta pureza, ideal para doces e bebidas.',1.79,6,1,0,1,'2025-04-12 00:55:21','importados','acucar_branco_1kg.jpg',0,10,'Unidade','000014',NULL,0.00,'','','ativo'),(2,'Agua Mineral 1.5L','Água mineral natural, garrafa de 1.5L, ideal para hidratação.',1.19,8,0,1,1,'2025-04-12 00:55:21','bebidas','agua_mineral_15l.jpg',100,10,'Unidade','000002','2025-05-31',19.92,'','','ativo'),(3,'Arroz Carolino 1kg','Arroz carolino tipo 1, soltinho e saboroso para todas as refeições.',1.34,1,1,0,1,'2025-04-12 00:55:21','importados','arroz_carolino_1kg.jpg',100,10,'Pacote','0000360',NULL,0.00,'','','ativo'),(4,'Atum Lata 120G','Atum em lata de 120g, ?timo para saladas e sanduíches.',2.23,4,0,0,1,'2025-04-12 00:55:21','importados','atum_lata_120g.jpg',100,10,'Unidade','0000255',NULL,0.00,'','','ativo'),(5,'Azeite Virgem Extra 750Ml','Azeite virgem extra importado, perfeito para temperos e culinária gourmet.',1.87,10,0,0,1,'2025-04-12 00:55:21','importados','azeite_virgem_extra_750ml.jpg',100,10,'Unidade','0000178',NULL,0.00,'','','ativo'),(6,'Bananas 1kg','Bananas frescas por quilo, ricas em pot?ssio e energia.',0.83,5,1,0,1,'2025-04-12 00:55:21','hortifruti','bananas_1kg.jpg',100,10,'Kg','000003',NULL,21.01,'','','ativo'),(7,'Batatas Cozer 2Kg','Batatas para cozer, ideais para pur?s e pratos portugueses.',1.66,5,0,0,1,'2025-04-12 00:55:21','hortifruti','batatas_cozer_2kg.jpg',50,10,'Pacote','0000159',NULL,0.00,'','','ativo'),(8,'Bolachas Maria 200G','Bolachas Maria, leve e crocante, pacote de 200g.',1.76,1,0,0,1,'2025-04-12 00:55:21','padaria','bolachas_maria_200g.jpg',100,10,'Pacote','0000155',NULL,0.00,'','','ativo'),(9,'Cafe Moido 250G','Café moído torrado, sabor intenso, pacote de 250g.',3.77,7,0,0,1,'2025-04-12 00:55:21','importados','cafe_moido_250g.jpg',100,10,'Pacote','0000210',NULL,0.00,'','','ativo'),(10,'Carne Frango Coxas 1kg','Coxas de frango congeladas, excelente fonte de proteína.',2.77,7,1,0,1,'2025-04-12 00:55:21','importados','carne_frango_coxas_1kg.jpg',50,10,'Pacote','0000240',NULL,0.00,'','','ativo'),(11,'Carne Porco Bifanas 500G','Bifanas de carne suína, macias e prontas para grelhar.',2.87,7,0,0,1,'2025-04-12 00:55:21','importados','carne_porco_bifanas_500g.jpg',100,10,'Pacote','0000127',NULL,0.00,'','','ativo'),(12,'Carne Vaca Bife 500G','Bife de carne bovina, suculento e selecionado.',3.95,7,0,0,1,'2025-04-12 00:55:21','importados','carne_vaca_bife_500g.jpg',100,10,'Pacote','0000140',NULL,0.00,'','','ativo'),(13,'Cebolas 1kg','Cebolas frescas direto da horta, sabor e qualidade garantidos.',2.16,5,1,0,1,'2025-04-12 00:55:21','hortifruti','cebolas_1kg.jpg',100,10,'Kg','000006',NULL,0.00,'','','ativo'),(14,'Cenouras Baby 500G','Cenouras baby crocantes, ideais para saladas e snacks.',1.86,5,0,0,1,'2025-04-12 00:55:21','hortifruti','cenouras_baby_500g.jpg',40,10,'Unidade','000210',NULL,0.00,'','','ativo'),(15,'Cerveja Mini 20X25Cl','Cerveja mini, embalagem com 20 unidades de 25cl.',4.02,8,0,0,1,'2025-04-12 00:55:21','bebidas','cerveja_mini_20x25cl.jpg',100,10,'Unidade','0000122',NULL,0.00,'','','ativo'),(16,'Chocolate Leite 100G','Chocolate ao leite cremoso, barra de 100g.',2.15,0,0,0,1,'2025-04-12 00:55:21','importados','chocolate_leite_100g.jpg',100,10,'Unidade','0000117',NULL,0.00,'','','ativo'),(17,'Detergente Lava Louca 1L','Detergente para lavar louças, fragrância suave, 1L.',2.42,6,0,1,1,'2025-04-12 00:55:21','limpeza','detergente_lava_louca_1l.jpg',100,10,'Unidade','0000250',NULL,0.00,'','','ativo'),(18,'Ervilhas Enlatado 400G','Ervilhas enlatadas, ideais para acompanhar pratos e saladas.',0.86,4,0,0,1,'2025-04-12 00:55:21','importados','ervilhas_enlatado_400g.jpg',100,10,'Unidade','0000124',NULL,0.00,'','','ativo'),(19,'Farinha Trigo 1kg','Farinha de trigo tipo 1, ideal para pães e bolos.',1.93,6,1,0,1,'2025-04-12 00:55:21','importados','farinha_trigo_1kg.jpg',100,10,'Unidade','0000120',NULL,0.00,'','','ativo'),(20,'Feijao Enlatado 420G','Feijão enlatado pronto para uso, rico em proteínas.',0.69,4,0,0,1,'2025-04-12 00:55:21','importados','feijao_enlatado_420g.jpg',100,10,'Unidade','0000118',NULL,0.00,'','','ativo'),(21,'Fiambre Peru 150G','Fiambre de peru fatiado, leve e delicioso.',1.53,7,0,0,1,'2025-04-12 00:55:21','hortifruti','fiambre_peru_150g.jpg',100,10,'Unidade','0000115',NULL,0.00,'','','ativo'),(22,'Grao Bico Enlatado 400G','Grão-de-bico enlatado, prático e nutritivo.',1.11,4,0,0,1,'2025-04-12 00:55:21','importados','grao_bico_enlatado_400g.jpg',0,10,'Unidade','000025',NULL,0.00,'','','ativo'),(23,'Iogurte Natural 4X125G','Iogurte natural, embalagem com 4 potes de 125g.',2.27,5,0,0,1,'2025-04-12 00:55:21','importados','iogurte_natural_4x125g.jpg',100,10,'Unidade','0000114',NULL,0.00,'','','ativo'),(24,'Laranjas 1kg','Laranjas frescas e suculentas, vendidas por quilo.',2.03,5,1,0,1,'2025-04-12 00:55:21','hortifruti','laranjas_1kg.jpg',50,10,'Unidade','0000112',NULL,0.00,'','','ativo'),(25,'Leite Condensado 397G','Leite condensado cremoso e doce, embalagem de 397g.',2.56,1,0,0,1,'2025-04-12 00:55:21','importados','leite_condensado_397g.jpg',100,10,'Unidade','0000111',NULL,0.00,'','','ativo'),(26,'Leite Uht Meio Gordo 1L','Leite UHT meio gordo, longa duração e sabor equilibrado.',1.75,1,0,1,1,'2025-04-12 00:55:21','importados','leite_uht_meio_gordo_1l.jpg',100,10,'Unidade','0000110',NULL,0.00,'','','ativo'),(27,'Maças Gala 1kg','Maçã gala vermelha, doces e crocantes.',0.82,5,1,0,1,'2025-04-12 00:55:21','hortifruti','macas_gala_1kg.jpg',20,5,'Unidade','0000107',NULL,0.00,'','','ativo'),(28,'Manteiga Com Sal 250G','Manteiga com sal tradicional, pote de 250g.',2.17,0,0,0,1,'2025-04-12 00:55:21','padaria','manteiga_com_sal_250g.jpg',100,10,'Unidade','0000106',NULL,0.00,'','','ativo'),(29,'Massa Espaguete 500G','Espaguete de trigo, pacote de 500g.',3.77,0,0,0,1,'2025-04-12 00:55:21','importados','massa_espaguete_500g.jpg',100,10,'Unidade','000109',NULL,0.00,'','','ativo'),(30,'Milho Doce Enlatado 3','Milho doce em lata, crocante e adocicado.',0.99,4,0,0,1,'2025-04-12 00:55:21','importados','milho_doce_enlatado_3.jpg',100,10,'Unidade','0000105',NULL,0.00,'','','ativo'),(31,'Oleo Girassol 1L','Óleo de girassol 1L, leve e saudável para frituras.',2.96,6,0,1,1,'2025-04-12 00:55:21','importados','oleo_girassol_1l.jpg',100,10,'Unidade','0000104',NULL,0.00,'','','ativo'),(32,'Ovos Classe M 12Un','Ovos classe M, cartela com 12 unidades.',1.62,1,0,0,1,'2025-04-12 00:55:21','hortifruti','ovos_classe_m_12un.jpg',100,10,'Pacote','0000103',NULL,0.00,'','','ativo'),(33,'Pao Forma Integral 500G','Pão de forma integral, rico em fibras, pacote de 500g.',2.88,6,0,0,1,'2025-04-12 00:55:21','padaria','pao_forma_integral_500g.jpg',100,10,'Pacote','0000102',NULL,0.00,'','','ativo'),(34,'Papel Higienico 12Rolos','Papel higiênico folha dupla, pacote com 12 rolos.',3.7,6,0,0,1,'2025-04-12 00:55:21','limpeza','papel_higienico_12rolos.jpg',100,10,'Pacote','0000101',NULL,0.00,'','','ativo'),(35,'Peito Peru Fatiado 150G','Peito de peru fatiado, prático e saboroso.',1.28,7,0,0,1,'2025-04-12 00:55:21','importados','peito_peru_fatiado_150g.jpg',100,10,'Pacote','000100',NULL,0.00,'','','ativo'),(36,'Donut Mercearia','Delicioso donut artesanal com cobertura rosa e granulados coloridos, embalado com charme e cuidado em caixa kraft personalizada da Mercearia. Ideal para um momento doce a qualquer hora!',1.19,1,0,0,1,'2025-04-12 00:55:21','padaria','produto_681bdb72f2e046.54330673.jpg',40,10,'Unidade','0000366',NULL,0.00,'','','ativo'),(37,'Queijo Curado Amanteigado 200G','Queijo curado amanteigado, porção de 200g, sabor intenso.',2.13,0,0,0,1,'2025-04-12 00:55:21','importados','queijo_curado_amanteigado_200g.jpg',100,10,'Pacote','000015',NULL,82.01,'','','ativo'),(38,'Queijo Flamengo Fatiado 200G','Queijo flamengo fatiado, perfeito para sandu?ches.',2.33,9,0,0,1,'2025-04-12 00:55:21','importados','queijo_flamengo_fatiado_200g.jpg',100,10,'Unidade','000010',NULL,0.00,'','','ativo'),(39,'Refrigerante Cola 1.5L','Refrigerante sabor cola, garrafa PET 1.5L.',1.75,8,0,1,1,'2025-04-12 00:55:21','bebidas','refrigerante_cola_15l.jpg',100,10,'L','000028',NULL,89.35,'','','ativo'),(40,'Sal Refinado 1kg','Sal refinado para uso culinário diário, 1kg.',1.19,6,1,0,1,'2025-04-12 00:55:21','importados','sal_refinado_1kg.jpg',100,10,'Pacote','000011',NULL,0.00,'','','ativo'),(41,'Salsichas Frankfurt 200G','Salsichas tipo Frankfurt, prontas para consumo.',4.32,6,0,0,1,'2025-04-12 00:55:21','importados','salsichas_frankfurt_200g.jpg',100,10,'Unidade','000009',NULL,0.00,'','','ativo'),(42,'Sardinha Lata 125G','Sardinhas em lata com Óleo, ricas em Ômega 3.',1.52,4,0,0,1,'2025-04-12 00:55:21','importados','sardinha_lata_125g.jpg',100,10,'Unidade','000036',NULL,0.00,'','','ativo'),(43,'Shampoo Neutro 400Ml','Shampoo neutro, indicado para todos os tipos de cabelo, 400ml.',1.97,6,0,0,1,'2025-04-12 00:55:21','limpeza','shampoo_neutro_400ml.jpg',100,10,'Unidade','000034',NULL,92.01,'','','ativo'),(44,'Sumo Laranja 1L','Sumo de laranja 100% natural, embalagem de 1L.',1.87,5,0,1,1,'2025-04-12 00:55:21','bebidas','sumo_laranja_1l.jpg',100,10,'L','000030',NULL,112.01,'','','ativo'),(45,'Tomate Pelado Enlatado 400G','Tomate pelado em lata, perfeito para molhos.',1.38,5,0,0,1,'2025-04-12 00:55:21','hortifruti','tomate_pelado_enlatado_400g.jpg',100,10,'Unidade','000032',NULL,91.01,'','','ativo'),(46,'Tomates 1kg','Tomates frescos, ideais para saladas e molhos.',1.32,5,1,0,1,'2025-04-12 00:55:21','hortifruti','tomates_1kg.jpg',100,10,'Unidade','000001',NULL,15.00,'','','ativo'),(47,'Vinho Tinto Alentejano 75Cl','Vinho tinto da região do Alentejo, 750ml.',1.54,8,0,0,1,'2025-04-12 00:55:21','bebidas','vinho_tinto_alentejano_75cl.jpg',100,10,'Unidade','000022','2028-11-25',0.00,'','','ativo'),(48,'Whisky Escoces 1L','Whisky Escocês tradicional, garrafa de 1L.',2.58,8,0,1,1,'2025-04-12 00:55:21','bebidas','whisky_escoces_1l.jpg',100,10,'Unidade','000020',NULL,0.00,'','','ativo'),(49,'Pao artesanal','Pão artesanal fresco, feito com fermento natural.',1.19,7,0.5,0,101,'2025-04-15 01:03:45','padaria','produto_681a92385ddd75.95015454.jpg',50,10,'Unidade','000021',NULL,0.00,'','','ativo'),(50,'Farofa Pronta Mercearia – 500g','A autêntica farofa pronta da Mercearia une o sabor brasileiro com a tradição portuguesa em uma embalagem prática e atrativa. Feita com farinha de mandioca torrada de alta qualidade, é ideal para acompanhar carnes, churrascos e refeições do dia a dia. O pacote de 500g exibe com destaque a logomarca da Mercearia, que celebra a união das bandeiras do Brasil e de Portugal, refletindo o compromisso com produtos que conectam culturas.',1.99,3,0,0,0,'2025-05-02 00:05:33','importados','produto_6813f02dd73804.86631532.png',100,10,'Pacote','000024',NULL,89.91,'','','ativo'),(51,'Desodorizante Antitranspirante Mercearia – Masculino e Feminino (150ml)','A linha de desodorizantes Mercearia combina proteção eficaz com identidade cultural. Disponível nas versões masculina (azul) e feminina (rosa), cada embalagem traz com orgulho a união entre Brasil e Portugal através de logomarcas distintas e modernas. Com fórmula antitranspirante e proteção por até 48 horas, garante frescor duradouro e confiança no dia a dia. Ideal para quem valoriza qualidade, praticidade e tradição luso-brasileira.',0.89,0,0,0,0,'2025-05-02 00:20:13','limpeza','produto_6813f39d343e10.63813723.jpg',100,10,'Unidade','000033',NULL,50.00,'','','ativo'),(54,'Sabonete Essência Pura','O sabonete \"Essência Pura\" é apresentado em uma barra retangular de tom creme suave, com o nome gravado em relevo. Sua embalagem, feita em papel kraft ecológico com detalhes em verde, destaca que o produto é dermatologicamente testado, ideal para todos os tipos de pele, hidratante, com pH balanceado e livre de parabenos. O visual transmite leveza, naturalidade e cuidado com a pele e o meio ambiente.',1.19,6,0,0,0,'2025-05-07 00:03:20','limpeza','produto_681a8728c21374.24666390.jpg',100,10,'Unidade','000037',NULL,77.01,'','','ativo'),(56,'Enxaguante Bucal PureMint Fresh','Enxaguante bucal PureMint Fresh com sabor hortelã suave, ação antisséptica, proteção por até 12h e fórmula sem álcool. Refrescância e cuidado diário em embalagem moderna.',1.89,6,0,0,0,'2025-05-07 00:11:04','limpeza','produto_681a88f8a00b67.30202894.jpg',100,10,'Unidade','000039',NULL,89.01,'','','ativo'),(57,'Creme dental Branco Puro Natura','Creme dental Branco Puro Natural com carvão ativado, sabor menta suave, sem flúor e sem parabenos. Limpeza profunda e branqueamento natural com proteção diária.',2.09,6,0,0,0,'2025-05-07 00:14:32','limpeza','produto_681a89c8bdb6d2.10184443.jpg',100,10,'Unidade','000038',NULL,67.99,'','','ativo'),(59,'Creme dental BrancoMax Tripla Ação','Creme dental BrancoMax Tripla Ação, com flúor ativo, menta refrescante e bicarbonato de sódio. Proporciona branqueamento seguro, proteção contra cáries e hálito fresco por mais tempo. Embalagem moderna em tons de branco, azul e verde, com tampa flip-top e design que transmite limpeza e cuidado completo.',1.87,6,0,0,0,'2025-05-07 00:18:20','limpeza','produto_681a8aacb6a216.54393007.jpg',100,10,'Unidade','000040',NULL,66.99,'','','ativo'),(60,'Água Aromatizada \"VittaLemon\" – Limão & Hortelã','Água aromatizada \"VittaLemon\" em garrafa de vidro transparente com fatias de limão e folhas de hortelã visíveis, rótulo minimalista em verde e branco, sem açúcar, sem gás, 100% natural e refrescante.',1.01,6,0,0,0,'2025-05-07 00:24:16','bebidas','produto_681a8c106f9534.56954132.jpg',100,10,'Unidade','000041',NULL,56.01,'','','ativo'),(61,'Pão artesanal Grano Rústico','O pão artesanal Grano Rústico apresenta uma casca dourada e crocante com cortes feitos à mão, revelando um miolo macio e aerado. Embalado em papel kraft com visor transparente e rótulo rústico, destaca-se por ser feito com fermentação natural lenta, sem aditivos e com ingredientes naturais, transmitindo autenticidade, sabor e tradição.',1.3,7,0,0,0,'2025-05-07 00:28:03','Padaria Artesanal','produto_681a8cf3a7e266.05541764.jpg',100,5,'Kg','000042',NULL,98.00,'','','ativo'),(62,'Croissant Artesanal Dourado','Croissant artesanal dourado, folhado à mão com manteiga premium, servido em prato rústico ao lado de uma embalagem kraft com selo tradicional. Ideal para um café da manhã elegante e acolhedor.',1.09,7,0,0,0,'2025-05-07 00:31:08','Padaria Artesanal','produto_681a8dac959de1.52689107.jpg',100,5,'Kg','000043',NULL,18.00,'','','ativo'),(63,'Focaccia doce Aurora de Canela','Pão artesanal rústico, feito com fermentação natural e ingredientes simples, com casca crocante e miolo macio, embalado em papel kraft com visual autêntico e acolhedor.',1.01,7,0,0,0,'2025-05-07 00:45:24','padaria','produto_681a9104ac4aa4.42177989.jpg',15,5,'Unidade','000045',NULL,15.00,'','','ativo'),(64,'Filão Brasileiro','Filão artesanal brasileiro, de formato alongado, casca fina e dourada com leves cortes no topo, miolo macio e aerado, servido em tábua rústica ao lado de uma embalagem kraft com selo tradicional.',1.05,7,0,0,0,'2025-05-07 00:53:12','padaria','produto_681a92d87b5f73.60673998.jpg',100,10,'Kg','000046',NULL,0.00,'','','ativo');
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promocao`
--

DROP TABLE IF EXISTS `promocao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promocao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `desconto` decimal(5,2) NOT NULL,
  `tipo` enum('percentual','fixo','compre2leve3','fretegratis','ofertadodia') DEFAULT 'percentual',
  `selo` varchar(100) DEFAULT NULL,
  `inicio` date DEFAULT NULL,
  `fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `vis_home` tinyint(1) DEFAULT 0,
  `vis_banner` tinyint(1) DEFAULT 0,
  `vis_pagina` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `promocao_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promocao`
--

LOCK TABLES `promocao` WRITE;
/*!40000 ALTER TABLE `promocao` DISABLE KEYS */;
INSERT INTO `promocao` VALUES (5,50,20.00,'percentual',NULL,'2025-05-07','2025-05-08',1,0,0,1),(6,3,40.00,'percentual','','2025-05-05','2025-05-12',1,1,1,1),(8,11,40.00,'percentual','Imperdível','2025-05-05','2025-05-11',1,1,1,1);
/*!40000 ALTER TABLE `promocao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `dt_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `senha` varchar(255) NOT NULL,
  `nivel_id` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.png',
  `usuario_nivel` varchar(50) NOT NULL DEFAULT 'cliente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (19,'Paula Maria','paula@paula.com','2025-04-15 16:12:22','$2y$10$KVBY2blpJglcrOlGWbCPnuX39MFBqZicM/YUPMzwyDT14TmodUM02',NULL,'perfil_681aa6e4b1fbe.jpg','cliente'),(20,'Alex ADM','alex@adm.com','2025-04-23 18:02:07','$2y$10$SneIQy8SxBjhcxheUuBa9ualo7ss5xFWbMaa4xHMVio0h48TD6dE2',1,'perfil_681aa00a105f2.jpg','admin');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-08  1:21:55
