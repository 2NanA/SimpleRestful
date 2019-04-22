DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `Username` varchar(255) NOT NULL,
    `Password` varchar(50) DEFAULT NULL,
    `NullIfDeleted` tinyint(1) DEFAULT '1',
    `Department` int(10) unsigned DEFAULT NULL,
    `Email` varchar(300) DEFAULT NULL,
    `CreatedBy` int(10) DEFAULT NULL,
    `CreatedDate` DateTime DEFAULT NULL,
    `LastModifiedBy` int(10) DEFAULT NULL,
    `LastModifiedDate` DateTime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;
