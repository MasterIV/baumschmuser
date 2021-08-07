<?php

if( isset( $_POST['options'] )) {
	$profil->saveData();
	throw new redirect( MODUL_SELF );
}

$view->box( $profil->getForm(MODUL_SELF), 'Profil', '500px' );
