<?php

function install() {
	db()->query("INSERT INTO `content_layer` (`id`, `name`, `status`, `parent`, `lft`, `rgt`, `template`)
			VALUES (1, 'Startseite', 0, NULL, 1, 2, NULL);");

	db()->query("INSERT INTO `content_panel` (`id`, `name`, `status`, `layer`, `group`, `script`, `template`, `prio`) VALUES
			(1, 'Menü', 0, 1, 'header', 'html', NULL, NULL),
			(2, 'Content', 0, 1, 'content', 'html', NULL, NULL)");

	db()->query("INSERT INTO `content_variable` (`name`, `panel`, `value`) VALUES
			('content', 1, '<a href=\"#\" class=\"a-navi\">Startseite</a> |\r\n<a href=\"#\" class=\"a-navi\">Über uns</a> |\r\n<a href=\"#\" class=\"a-navi\">Kontakt</a> |\r\n<a href=\"#\" class=\"a-navi\">Impressum</a>  '),
			('content', 2, '<div class=\"text\">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</div>');");
}

function remove() {
	db()->query("DELETE FROM content_layer WHERE id = 1");
}
