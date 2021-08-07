<?php

$rc =  new data_controller( 'user_profil', MODUL_SELF );
$rc->add( 'id', 'Id', 1, 0, 0, 0 );
$rc->add( 'name', 'Name', 1, 1, 1, 1 );
$rc->add( 'caption', 'Beschriftung', 1, 1, 1, 1 );
$rc->add( 'value', 'Standardwert', 0, 1, 1, 0 );

$rc->add( 'type', 'Typ', 0, 1, 1, 1, 'select', array(
	'text' => 'Einzeiliger Text',
	'textarea' => 'Mehrzeiliger Text',
	'checkbox' => 'Checkbox',
	'select' => 'Auswahlfeld'
));

$rc->add( 'options', 'Auswahlmöglichkeiten', 0, 1, 1, 0, 'textarea');

if( !empty( $_GET['delete'] )) {
	$field = $db->user_profil->row( $_GET['delete'] )->assoc();
	$db->user_details->del("category = 'profil' AND name = '%s'", $field['name']);
}

if( !empty( $_GET['update'] )) {
	$field = $db->user_profil->row( $_GET['update'] )->assoc();
	$db->user_details->update(array( 'name' => $_POST['name'] ), "category = 'profil' AND name = '%s'", $field['name']);
}

if( $rc->run()) throw new redirect(MODUL_SELF);

$grid = $view->grid();
$grid[0]->box( $rc->get_list(), 'Felder verwalten' );
$grid[1]->box( $rc->get_form(), 'Feld '.(empty($_GET['edit']) ? 'erstellen' : 'bearbeiten' ));
