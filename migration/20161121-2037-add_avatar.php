<?php

function install() {
	db()->query("ALTER TABLE user_data ADD COLUMN avatar tinyint(1) DEFAULT 0;");
}

function remove() {
	db()->query("ALTER TABLE user_data DROP COLUMN avatar");
}
