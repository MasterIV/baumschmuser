<?php

function install() {
	db()->query("ALTER TABLE `update_package`
		ADD `installations` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `version` ,
		ADD `updates` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `installations` ;");
}

function remove() {
	db()->query("ALTER TABLE `update_package`
		DROP COLUMN `installations`,
		DROP COLUMN  `updates`;");
}
