<?php

function install() {
	db()->query("ALTER TABLE update_package ADD COLUMN forward TINYINT(1) DEFAULT 0;");
	db()->query("ALTER TABLE update_server ADD COLUMN forward TINYINT(1) DEFAULT 0;");
}

function remove() {
	db()->query("ALTER TABLE update_package DROP COLUMN forward");
	db()->query("ALTER TABLE update_server DROP COLUMN forward");
}
