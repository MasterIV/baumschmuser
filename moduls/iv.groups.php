<?php

$rights = iv::get('rights');

$_POST['rights'] = serialize($_POST['r']);
$rc = new data_controller( 'user_groups', MODUL_SELF );
$rc->add( 'name', 'Name', 1, 1, 1, 1 );
$rc->add( 'rights', 'Rechte', 0, 0, 1, 0, 'hidden' );
if( $rc->run()) throw new redirect( MODUL_SELF.'&edit='.intval( $_GET['update'] ));

$grid = $view->grid();
$grid[0]->box( $rc->get_create(), 'Gruppe erstellen' );
$grid[0]->box( $rc->get_list(), 'Gruppen verwalten' );

if( !empty( $_GET['edit'] ) && $group = db()->id_get('user_groups', $_GET['edit'] )) {
	$form = $rc->get_edit( $_GET['edit'] );
	$form->append( $tabs = new tabs());
	$grouprights = (array) unserialize($group['rights']);

	foreach( $rights->providers as $type => $provider ) {
		$inputs = array();
		$typerights = $grouprights[$type]?: array();

		foreach( $provider->keys() as $key => $caption )
			if( !isset( $provider->always[$key] )) {
				$inputs[] = $inp = new form_field_boxtree( "r[{$type}][{$key}]", $caption, isset( $typerights[$key] ));
				foreach( $provider->flagNames( $key ) as $flag => $caption )
					$inp->sub( "r[{$type}][{$key}][{$flag}]", $caption, isset( $typerights[$key][$flag] ));
			}

		$tabs->add( $provider->name, implode( $inputs ));
	}

	$grid[1]->box( $form, 'Rechte bearbeiten' );
} else {
	$grid[1]->box( 'Bitte wählen sie eine Gruppe zur Bearbeiung aus', 'Systemhinweis' );
}

