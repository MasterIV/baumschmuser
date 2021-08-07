<?php

if( isset( $_POST['login_name'] ))
	if( $session->login( $_POST['login_name'], $_POST['login_pass'],  $_POST['relogin'], 1 )) {
		throw new redirect($_POST['ref'] && !strpos($_POST['ref'], 'logout') ? $_POST['ref'] : 'index.php');
	} else {
		echo '<div class="error">Userdaten ungültig</div>';
	}

$form = new form_renderer(PAGE_SELF, 'Login');
$form->text('login_name', 'Username');
$form->password('login_pass', 'Passwort');
$form->checkbox('relogin', 'Eingeloggt bleiben');
$form->hidden('ref', $_SERVER['HTTP_REFERER']);
echo $form;