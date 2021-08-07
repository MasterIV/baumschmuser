<?php

function install() {
	db()->query("CREATE TABLE IF NOT EXISTS `comments` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`thread` varchar(1024) NOT NULL,
		`text` text NOT NULL,
		`user` int(10) unsigned NOT NULL,
		`date` int(10) unsigned NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	db()->query("ALTER TABLE `comments` ADD FOREIGN KEY (`user`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
}

function remove() {
	db()->query("DROP TABLE IF EXISTS `comments`;");
}
