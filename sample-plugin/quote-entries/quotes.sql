-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 01, 2015 at 07:40 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `frc`
--

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE IF NOT EXISTS `quotes` (
  `QuoteID` int(11) NOT NULL,
  `Number` int(11) NOT NULL AUTO_INCREMENT,
  `TypeID` int(11) NOT NULL,
  `OrigZip` varchar(50) NOT NULL,
  `OrigCity` varchar(100) NOT NULL,
  `OrigState` varchar(50) NOT NULL,
  `OrigCountryID` int(11) NOT NULL,
  `OrigLocationType` int(11) NOT NULL,
  `OrigLiftGate` tinyint(1) NOT NULL,
  `OrigInside` tinyint(1) NOT NULL,
  `OrigContact` varchar(50) NOT NULL,
  `OrigBusiness` varchar(50) NOT NULL,
  `OrigAddress` varchar(255) NOT NULL,
  `OrigPhone` varchar(50) NOT NULL,
  `OrigFax` varchar(50) NOT NULL,
  `OrigEmail` varchar(255) NOT NULL,
  `DestZip` varchar(50) NOT NULL,
  `DestCity` varchar(50) NOT NULL,
  `DestState` varchar(50) NOT NULL,
  `DestContact` varchar(50) NOT NULL,
  `DestBusiness` varchar(100) NOT NULL,
  `DestAddress` varchar(100) NOT NULL,
  `DestPhone` varchar(50) NOT NULL,
  `DestFax` varchar(50) NOT NULL,
  `DestEmail` varchar(255) NOT NULL,
  `DestLocationType` int(11) NOT NULL,
  `DestLiftGate` tinyint(1) NOT NULL,
  `DestInside` tinyint(1) NOT NULL,
  `DestCountryID` int(11) NOT NULL,
  `DestCityName` varchar(255) NOT NULL,
  `DestServicePointID` int(11) NOT NULL,
  `TrailerTypeID` int(11) NOT NULL,
  `TrailerPartType` int(11) NOT NULL,
  `AmountOfTrailer` decimal(10,2) NOT NULL,
  `trailerFeet` varchar(50) NOT NULL,
  `ReadyDate` varchar(255) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Company` varchar(100) NOT NULL,
  `Phone` varchar(50) NOT NULL,
  `PhoneExt` varchar(50) NOT NULL,
  `Fax` varchar(50) NOT NULL,
  `FaxExt` varchar(50) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Mileage` float NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `originalprice` decimal(10,2) NOT NULL,
  `Days` varchar(50) NOT NULL,
  `ShipmentCost` decimal(10,2) NOT NULL,
  `CreditCardCharge` decimal(10,2) NOT NULL,
  `GrossProfit` decimal(10,2) NOT NULL,
  `Carrier` varchar(100) NOT NULL,
  `CarrierContact` varchar(100) NOT NULL,
  `CarrierPhone` varchar(50) NOT NULL,
  `DeliveryDate` varchar(255) NOT NULL,
  `QuoteDate` datetime NOT NULL,
  `UserID` int(11) NOT NULL,
  `CallIn` tinyint(1) NOT NULL,
  `ShipDocs` tinyint(1) NOT NULL,
  `SpecialInstructions` text NOT NULL,
  `ContactAddress` varchar(100) NOT NULL,
  `ContactZip` varchar(50) NOT NULL,
  `BillFirstName` varchar(100) NOT NULL,
  `BillLastName` varchar(100) NOT NULL,
  `BillAddress` varchar(100) NOT NULL,
  `BillZip` varchar(50) NOT NULL,
  `BillPhone` varchar(100) NOT NULL,
  `BillFax` varchar(50) NOT NULL,
  `BillEmail` varchar(255) NOT NULL,
  `Data` text NOT NULL,
  `ltlshippingmethodname` varchar(255) NOT NULL,
  PRIMARY KEY (`Number`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`QuoteID`, `Number`, `TypeID`, `OrigZip`, `OrigCity`, `OrigState`, `OrigCountryID`, `OrigLocationType`, `OrigLiftGate`, `OrigInside`, `OrigContact`, `OrigBusiness`, `OrigAddress`, `OrigPhone`, `OrigFax`, `OrigEmail`, `DestZip`, `DestCity`, `DestState`, `DestContact`, `DestBusiness`, `DestAddress`, `DestPhone`, `DestFax`, `DestEmail`, `DestLocationType`, `DestLiftGate`, `DestInside`, `DestCountryID`, `DestCityName`, `DestServicePointID`, `TrailerTypeID`, `TrailerPartType`, `AmountOfTrailer`, `trailerFeet`, `ReadyDate`, `FirstName`, `LastName`, `Company`, `Phone`, `PhoneExt`, `Fax`, `FaxExt`, `Email`, `Mileage`, `Price`, `originalprice`, `Days`, `ShipmentCost`, `CreditCardCharge`, `GrossProfit`, `Carrier`, `CarrierContact`, `CarrierPhone`, `DeliveryDate`, `QuoteDate`, `UserID`, `CallIn`, `ShipDocs`, `SpecialInstructions`, `ContactAddress`, `ContactZip`, `BillFirstName`, `BillLastName`, `BillAddress`, `BillZip`, `BillPhone`, `BillFax`, `BillEmail`, `Data`, `ltlshippingmethodname`) VALUES
(0, 1, 3, '12304', 'Schenectady', 'NY', 0, 7, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 7, 0, 0, 23, 'Cartagena', 0, 0, 0, '0.00', '', '07/01/2015', 'sdfdsdfs', 'sdfdssdf', 'sdfdsf', '324-324-3243', '', '432-434-3232', '', 'sdfdsf@gmai.com', 0, '0.00', '0.00', '', '0.00', '0.00', '0.00', '', '', '', '', '2015-06-30 12:15:05', 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '[{"number":1,"count":1,"goodsType":1,"stackable":0,"weightUnit":"Lbs","packageType":"75","length":"48","width":"48","height":"22","weight":"12","description":"df","classID":"55"}]', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
