DROP TABLE IF EXISTS `CupGoTabOrders`;

CREATE TABLE `CupGoTabOrders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ProductId` int(11) DEFAULT NULL,
  `TabId` int(11) DEFAULT NULL,
  `OrderNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `TabId` (`TabId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
