<?php

if( !empty( $_POST )) {
	$_POST['captcha'] = isset( $_POST['captcha'] );
	$panelvars = array_intersect_key( $_POST, array_flip( array(
		'theme', 'captcha'
	)));
}

$form = new form_renderer(EDITOR_SELF);

$form->checkbox('captcha', 'Use Captcha', $panelvars['captcha'] );
$form->select('theme', 'Recaptcha Theme', array(
	'red' => 'red',
	'white' => 'white',
	'blackglass' => 'blackglass',
	'clean' => 'clean',
), $panelvars['theme'] );

$form->text( 'passlen', 'Passwort Länge', $panelvars['passlen'] );


$view->box( $form, "Registrierungseinstellungen" );

