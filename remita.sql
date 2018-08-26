/*
Navicat MySQL Data Transfer

Source Server         : Kraneum
Source Server Version : 100130
Source Host           : localhost:3306
Source Database       : remita

Target Server Type    : MYSQL
Target Server Version : 100130
File Encoding         : 65001

Date: 2018-08-26 23:41:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for customers_tbl
-- ----------------------------
DROP TABLE IF EXISTS `customers_tbl`;
CREATE TABLE `customers_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payerName` varchar(255) NOT NULL,
  `payerEmail` varchar(255) NOT NULL,
  `payerPhone` varchar(255) NOT NULL,
  `payerBankCode` varchar(255) NOT NULL,
  `payerAccount` varchar(255) NOT NULL,
  `amount` int(255) NOT NULL,
  `startDate` varchar(50) NOT NULL,
  `endDate` varchar(50) NOT NULL,
  `mandateType` varchar(50) NOT NULL,
  `maxNoOfDebits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customers_tbl
-- ----------------------------
INSERT INTO `customers_tbl` VALUES ('1', 'Samuel Samuel', 'sam.sam@gmail.com', '07062000000', '044', '0000000000', '5000', '01/09/2018', '21/08/2019', 'DD', '3');

-- ----------------------------
-- Table structure for mandate_tbl
-- ----------------------------
DROP TABLE IF EXISTS `mandate_tbl`;
CREATE TABLE `mandate_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerId` varchar(250) NOT NULL,
  `mandateId` varchar(250) NOT NULL,
  `requestId` varchar(250) NOT NULL,
  `remitaTransRef` varchar(255) NOT NULL,
  `activationStatus` int(11) NOT NULL,
  `activationDate` date NOT NULL DEFAULT '0000-00-00',
  `startDate` date NOT NULL DEFAULT '0000-00-00',
  `endDate` date NOT NULL DEFAULT '0000-00-00',
  `amount` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of mandate_tbl
-- ----------------------------

-- ----------------------------
-- Table structure for transactions_tbl
-- ----------------------------
DROP TABLE IF EXISTS `transactions_tbl`;
CREATE TABLE `transactions_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerId` varchar(255) NOT NULL,
  `mandateId` varchar(255) NOT NULL,
  `requestId` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `RRR` varchar(255) NOT NULL,
  `transactionRef` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `debitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of transactions_tbl
-- ----------------------------
