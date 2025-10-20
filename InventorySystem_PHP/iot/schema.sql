-- DHT11 readings table
CREATE TABLE IF NOT EXISTS `dht11_readings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temperature` float DEFAULT NULL,
  `humidity` float DEFAULT NULL,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


