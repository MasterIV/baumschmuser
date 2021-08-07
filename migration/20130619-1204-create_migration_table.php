<?php

function install() {
	db()->query("CREATE TABLE IF NOT EXISTS `update_migration` (
			`id` varchar(250) NOT NULL,
			`create_date` int(10) unsigned NOT NULL,
			`create_by` int(10) unsigned NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

function remove() {
	db()->query("DROP TABLE `update_migration`;");
}
