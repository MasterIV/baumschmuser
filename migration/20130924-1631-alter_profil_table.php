<?php

function install() {
	db()->query("ALTER TABLE `user_profil`
		CHANGE `options` `options` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
		ADD `value` VARCHAR( 255 ) NOT NULL AFTER `type`;");

	db()->query("DROP TABLE IF EXISTS `user_details`;");

	db()->query("
		CREATE TABLE `user_details` (
        `user` int(10) unsigned NOT NULL,
        `category` varchar(32) NOT NULL,
        `name` varchar(32) NOT NULL,
        `value` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`user`,`category`,`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	db()->query("ALTER TABLE `user_details`
			ADD FOREIGN KEY (`user`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
}

function remove() {
	db()->query("DROP TABLE IF EXISTS `user_details`;");
}
