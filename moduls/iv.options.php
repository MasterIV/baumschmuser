<?php

if( isset( $_POST['options'] )) {
	$conf->saveData();
	throw new redirect( MODUL_SELF );
}

$view->box( $conf->getForm(MODUL_SELF), 'Optionen bearbeiten', '600px' );
