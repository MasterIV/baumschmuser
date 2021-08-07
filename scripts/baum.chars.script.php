<?php

$rc =  new data_controller( 'raid_chars', PAGE_SELF );

$rc->prefix = 'char_';
$rc->condition = 'owner = '.current_user();
$rc->auto['create'] = [
		'owner' => current_user(),
		'level' => 70
];

$rc->add( 'id', 'Id', 1, 0, 0, 0 );
$rc->add( 'name', 'Name', 1, 1, 1, 1 );

$rc->add( 'classid', 'Klasse', 1, 1, 1, 1, 'select', $GLOBALS['klassen_filtered']  );
$rc->add( 'raceid', 'Rasse', 0, 1, 1, 0, 'select', $GLOBALS['rassen_horde']  );
$rc->add( 'genderid', 'Geschlecht', 0, 1, 1, 0, 'select', $GLOBALS['gender'] );
$rc->add( 'rolle', 'Rolle', 1, 1, 1, 0, 'select', $GLOBALS['rollen']);


$rc->run();

echo '<h2>Charaktere</h2>';
echo $rc->get_list();

if(isset($_GET['char_edit']))
	echo '<h2>Charakter bearbeiten</h2>';
else
	echo '<h2>Charakter erstellen</h2>';

echo $rc->get_form();

