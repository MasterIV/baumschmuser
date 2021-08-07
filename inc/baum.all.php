<?php

$klassen = [
		'Unknonw',
		'Krieger',
		'Paladin',
		'Jäger',
		'Schurke',
		'Priester',
		'Todesritter',
		'Schamane',
		'Magier',
		'Hexenmeister',
		'Unbekannt',
		'Druide',
];

$rollen = [
		'Melee',
		'Ranged',
		'Tank',
		'Heal'
];

$rassen = [
		'Unknown',
		'Mensch',
		'Orc',
		'Zwerg',
		'Nachtelf',
		'Untot',
		'Taure',
		'Gnom',
		'Troll',
		'Goblin',
		'Blutelf',
		'Draenei',
];

$rassen_horde = array_intersect_key($rassen, array_flip([2, 5, 6, 8, 10]));
$rassen_ally = array_intersect_key($rassen, array_flip([1, 3, 4, 7, 11]));
$klassen_filtered = array_intersect_key($klassen, array_flip([1, 2, 3, 4, 5, 7, 8, 9, 11]));

// Klassenfarben
$klassen_farben = [
		"#CCCCCC",
		"#C69B6D",
		"#F48CBA",
		"#AAD372",
		"#FFF468",
		"#FFFFFF",
		"#AA0000",
		"#1A3CAA",
		"#68CCEF",
		"#9382C9",
		"#CCCCCC",
		"#FF7C0A",
];

$gender = [
		'Männlich',
		'Weiblich',
];

$raids = [
		'Karazhan',
		'Gruul',
		'Magtheridon',
		'Festung der Stürme',
		'Schlangenschrein',
		'Schlacht um Hyjal',
		'Black Temple',
		'Zul\'Aman',
];
