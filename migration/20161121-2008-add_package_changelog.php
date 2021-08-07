<?php

function install() {
	db()->query("CREATE TABLE IF NOT EXISTS `update_change_log` (
		  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `package` varchar(200) NOT NULL,
		  `comment` text NOT NULL,
		  `create_by` int(10) unsigned DEFAULT NULL,
		  `create_date` int(10) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `package` (`package`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

function remove() {
	db()->query("DROP TABLE `update_change_log`;");
}
