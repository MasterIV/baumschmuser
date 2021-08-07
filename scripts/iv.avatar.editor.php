<?php

if( !empty( $_POST )) {
	$_POST['resize'] = isset( $_POST['resize'] );
	$panelvars = array_intersect_key( $_POST, array_flip( array(
		'max_width', 'max_height', 'resize'
	)));
}

$form = new form_renderer(EDITOR_SELF);

$form->checkbox( 'resize', 'Resize', $panelvars['resize'] );
$form->text( 'max_width', 'Maximale Breite', $panelvars['max_width'] );
$form->text( 'max_height', 'Maximale Höhe', $panelvars['max_height'] );

$view->box( $form, "Avatareinstellungen" );

