-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2023 at 11:17 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `420dw3_project`
--
CREATE DATABASE IF NOT EXISTS `420dw3_project` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `420dw3_project`;

-- --------------------------------------------------------

--
-- Table structure for table `cart_products`
--

DROP TABLE IF EXISTS `cart_products`;
CREATE TABLE IF NOT EXISTS `cart_products` (
  `cartId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `dateAdded` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  UNIQUE KEY `CartProducts_cart_product_ids_UNIQ` (`cartId`,`productId`),
  KEY `CartProducts_cartId_INDX` (`cartId`),
  KEY `CartProducts_productId_INDX` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `passwordHash` varchar(128) NOT NULL,
  `dateCreated` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  PRIMARY KEY (`id`),
  UNIQUE KEY `Customer_Username_UNIQ` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `username`, `passwordHash`, `dateCreated`) VALUES
(1, 'myUser', 'hlbiasfhblvahbklvaehbilavhblivae', '2023-02-14 09:53:35.574');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` enum('created','placed','completed','cancelled') NOT NULL DEFAULT 'created',
  `customerId` int(11) NOT NULL,
  `cartId` int(11) DEFAULT NULL,
  `billingAddress` varchar(256) DEFAULT NULL,
  `shippingAddress` varchar(256) DEFAULT NULL,
  `dateCreated` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  PRIMARY KEY (`id`),
  KEY `Orders_status_INDX` (`status`),
  KEY `Orders_customerId_INDX` (`customerId`),
  KEY `Orders_cartId_INDX` (`cartId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayName` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  `imageUrl` varchar(128) DEFAULT NULL,
  `unitPrice` decimal(15,2) NOT NULL DEFAULT 0.00,
  `availableQty` int(11) NOT NULL DEFAULT 0,
  `dateCreated` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shopping_carts`
--

DROP TABLE IF EXISTS `shopping_carts`;
CREATE TABLE IF NOT EXISTS `shopping_carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` enum('created','ordered') NOT NULL DEFAULT 'created',
  `dateCreated` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  PRIMARY KEY (`id`),
  KEY `ShoppingCard_status_INDX` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_products`
--
ALTER TABLE `cart_products`
  ADD CONSTRAINT `FK_CArtProducts_Products_productId` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CartProducts_Carts_cartId` FOREIGN KEY (`cartId`) REFERENCES `shopping_carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_Orders_Carts_cartId` FOREIGN KEY (`cartId`) REFERENCES `shopping_carts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Orders_Customers_CustomerId` FOREIGN KEY (`customerId`) REFERENCES `customers` (`id`) ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
