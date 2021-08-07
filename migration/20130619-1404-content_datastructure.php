<?php

function install() {
	db()->query("CREATE TABLE IF NOT EXISTS `content_layer` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(200) NOT NULL,
			`status` tinyint(3) unsigned NOT NULL,
			`parent` int(10) unsigned DEFAULT NULL,
			`lft` int(10) unsigned NOT NULL,
			`rgt` int(10) unsigned NOT NULL,
			`template` varchar(200) DEFAULT NULL,
			`create_date` int(10) unsigned DEFAULT NULL,
			`create_by` int(10) unsigned DEFAULT NULL,
			`update_date` int(10) unsigned DEFAULT NULL,
			`update_by` int(10) unsigned DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `parent` (`parent`),
			KEY `lft` (`lft`),
			KEY `rgt` (`rgt`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

	db()->query("CREATE TABLE IF NOT EXISTS `content_panel` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(200) NOT NULL,
			`status` tinyint(3) unsigned NOT NULL,
			`layer` int(10) unsigned DEFAULT NULL,
			`group` varchar(200) DEFAULT NULL,
			`script` varchar(200) NOT NULL,
			`template` varchar(200) DEFAULT NULL,
			`prio` tinyint(3) unsigned DEFAULT '100',
			`create_date` int(10) unsigned DEFAULT NULL,
			`create_by` int(10) unsigned DEFAULT NULL,
			`update_date` int(10) unsigned DEFAULT NULL,
			`update_by` int(10) unsigned DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `layer` (`layer`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

	db()->query("CREATE TABLE IF NOT EXISTS `content_variable` (
			`name` varchar(32) NOT NULL,
			`panel` int(10) unsigned NOT NULL,
			`value` text,
			PRIMARY KEY (`panel`,`name`),
			KEY `panel` (`panel`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	db()->query("ALTER TABLE `content_layer`
  		ADD FOREIGN KEY (`parent`) REFERENCES `content_layer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
	db()->query("ALTER TABLE `content_panel`
  		ADD FOREIGN KEY (`layer`) REFERENCES `content_layer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
	db()->query("ALTER TABLE `content_variable`
  		ADD FOREIGN KEY (`panel`) REFERENCES `content_panel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
}

function remove() {
	db()->query("DROP TABLE `content_variable` ;");
	db()->query("DROP TABLE `content_panel`;");
	db()->query("DROP TBALE `content_layer`;");
}
