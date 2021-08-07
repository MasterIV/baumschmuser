<?php

function install() {
	db()->query("CREATE TABLE IF NOT EXISTS `base_conf` (
			`category` varchar(32) NOT NULL,
			`name` varchar(32) NOT NULL,
			`value` varchar(255) DEFAULT NULL,
			`create_date` int(10) unsigned DEFAULT NULL,
			`create_by` int(10) unsigned DEFAULT NULL,
			PRIMARY KEY (`category`,`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	db()->query("CREATE TABLE IF NOT EXISTS `base_menu_category` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`user` int(10) unsigned NOT NULL,
			`name` varchar(50) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	db()->query("CREATE TABLE IF NOT EXISTS `base_menu_point` (
			`user` int(10) unsigned NOT NULL,
			`modul` varchar(200) NOT NULL,
			`category` int(10) unsigned NULL,
			PRIMARY KEY (`user`,`modul`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	db()->query("ALTER TABLE base_menu_point
		ADD FOREIGN KEY (user) REFERENCES user_data(id) ON UPDATE CASCADE ON DELETE CASCADE,
		ADD FOREIGN KEY (category) REFERENCES base_menu_category(id) ON UPDATE CASCADE ON DELETE CASCADE;");

	db()->query("ALTER TABLE base_menu_category
		ADD FOREIGN KEY (user) REFERENCES user_data(id) ON UPDATE CASCADE ON DELETE CASCADE;");
}

function remove() {
	db()->query("DROP TABLE `base_conf`;");
	db()->query("DROP TABLE `base_menu_point` ; ");
	db()->query("DROP TABLE `base_menu_category` ; ");
}
