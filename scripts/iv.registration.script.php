<script type="text/javascript">
	var RecaptchaOptions = { theme : '<?php echo $panelvars['theme'] ?: 'white'; ?>' };
</script><?php

require_once 'inc/recaptchalib.php';
$error = '<div class="error">%s</div>';

// @TODO: Passwort Generieren
// @TODO: MailAktivierung

$minlen = intval( $panelvars['passlen'] );
$usecap = $panelvars['captcha'];

if( !empty( $_POST )) {
	// Name Validation
	if( empty( $_POST['register_name'] ))
		printf( $error, 'Bitte Namen angeben' );
	if( preg_match( '/[^\w\d]/', $_POST['register_name'], $m ))
		printf( $error, 'Der Name enthält ungültige Zeichen: '.htmlspecialchars( $m[0] ));

	// Email Validation
	elseif( empty( $_POST['register_mail'] ))
		printf( $error, 'Bitte E-Mail angeben' );
	elseif( !$mail = filter_var($_POST['register_mail'], FILTER_VALIDATE_EMAIL))
		printf( $error, 'Die angegebene E-Mail ist ungültig' );

	// Password Validation
	elseif( empty( $_POST['register_pass'] ))
		printf( $error, 'Bitte Passwort wählen' );
	elseif( $minlen && strlen( $_POST['register_pass'] ) < $minlen )
		printf( $error, 'Ihr Passwort muss mindestens '.$minlen.' Zeichen enthalten' );
	elseif( $_POST['register_pass'] != $_POST['register_repetition'] )
		printf( $error, 'Passwort und Wiederholung stimmen nicht überein' );

	// Captcha Validitern
	elseif( $usecap && !recaptcha_check_answer( $recaptcha_privatekey,
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"] )->is_valid)
		printf( $error, 'Captcha ist inkorrekt' );

	// Blacklists prüfen
	elseif( $db->query( "SELECT 1 FROM `user_blocked`
			WHERE '%s' LIKE CONCAT('%%', `pattern`,'%%')
			AND `type` = 'name'", $_POST['register_name'] )->num_rows())
		printf( $error, 'Der Username ist unzulässig' );
	elseif( $db->query( "SELECT 1 FROM `user_blocked`
			WHERE '%s' LIKE CONCAT('%%', `pattern`,'%%')
			AND `type` = 'email'", $_POST['register_mail'] )->num_rows())
		printf( $error, 'Die E-Mail ist unzulässig' );

	// Already in use?
	elseif( $db->id_get( 'user_data',$_POST['register_mail'], 'email'))
		printf( $error, 'Die angegebene E-Mail ist bereits vergeben' );
	elseif( $db->id_get( 'user_data',$_POST['register_name'], 'name'))
		printf( $error, 'Der angegebene Name ist bereits vergeben' );

	// Do the registration
	else {
		$db->insert('user_data', array(
			'name' => $_POST['register_name'],
			'email' => $_POST['register_mail'],
			'pass_salt' => $salt = uniqid(),
			'pass_hash' => session_iv::crypt($_POST['register_pass'], $salt),
			'type' => 1
		));

		// @TODO: Registration Mail

		throw new redirect( PAGE_SELF.'completed' );
	}
}

if( isset( $_GET['completed'] )) {
	echo '<p>Registration erfolgreich und so...</p>';
} else {
	$captcha = '<div class="control-group"><label class="control-label" for="form_field_3">Captcha</label>'.
		'<div class="controls">'.recaptcha_get_html( $recaptcha_publickey ).'</div></div>';

$form = new form_renderer(PAGE_SELF);
$form->text('register_name', 'Username', $_POST['register_name'] );
$form->password('register_pass', 'Passwort' );
$form->password('register_repetition', 'Wiederholung' );
$form->text('register_mail', 'E-Mail', $_POST['register_mail'] );

if( $usecap )
	$form->append( $captcha );

echo $form;
}

