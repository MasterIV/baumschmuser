<?php

function install() {
	db()->query("ALTER TABLE `content_layer` ADD `link` VARCHAR( 250 ) NULL AFTER `template` ;");
}

function remove() {
	db()->query("ALTER TABLE `content_layer` DROP COLUMN `link`;");
}
