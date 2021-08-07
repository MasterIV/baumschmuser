<?php

$rc =  new data_controller( 'user_data', MODUL_SELF );
$rc->add( 'id', 'Id', 1, 0, 0, 0 );
$rc->add( 'name', 'Name', 1, 1, 1, 1 );
$rc->add( 'email', 'E-Mail' );
$rc->edit = $modulrights['edit'];

if( $_POST['search'] ) {
	foreach( $_POST['search'] as $key => $value )
		if( !empty( $value ))
			$rc->condition .= db()->format(" AND `%s` LIKE '%s%%'", $key, $value );
}

if( $modulrights['profil'])
	$rc->option('assets/small/user_information.png', 'profil', 'Profil');

if( $rc->run()) throw new redirect( MODUL_SELF );

$searchForm = new form(MODUL_SELF);
$searchForm->text('search[id]', 'ID', $_POST['search']['id'] );
$searchForm->text('search[name]', 'Name', $_POST['search']['name'] );
$searchForm->text('search[email]', 'E-Mail', $_POST['search']['email'] );

$grid = $view->grid();
$grid[0]->box( $searchForm, 'User suchen' );
$grid[0]->box( $rc->get_list(), 'User verwalten' );

if( !empty( $_GET['profil'] ) && $modulrights['profil']) {
	$edituser = db()->id_get('user_data', $_GET['profil'] );
	$uri = MODUL_SELF.'&profil='.$edituser['id'];
	$userprofil = new data_options('user_details', $profilInfo, array( 'user' => $edituser['id'] ));

	if( isset( $_POST['options'] )) {
		$userprofil->saveData();
		throw new redirect( $uri );
	}

	$form = $userprofil->getForm($uri);
	$form->linkbutton('Abbrechen', MODUL_SELF);
	$grid[1]->box( $form, 'Profil', '500px' );
} elseif( empty( $_GET['edit'] ) || !$modulrights['edit']) {
	$grid[1]->box( $rc->get_create(), 'User erstellen' );
} else {
	$form = $rc->get_edit( $_GET['edit'] );
	$edituser = db()->id_get('user_data', $_GET['edit'] );
	$tabs = new tabs();
	$uri = MODUL_SELF.'&edit='.$edituser['id'];

	if( $modulrights['password'] ) {
		$i++;

		if( !empty( $_POST['pass_new']) && $_POST['pass_new'] == $_POST['pass_rep']) {
			$pass = session_iv::crypt($_POST['pass_new'], $salt = uniqid());
			db()->id_update('user_data', array('pass_type' => 0, 'pass_hash' => $pass, 'pass_salt' => $salt), $_GET['edit']);
			$msg = alert( 'Passwort geändert.', 'success' );
		}

		$passform = new form_renderer( $uri.'#tabs-'.$i.'-hash' );
		$passform->password('pass_new', 'Neues Passwort');
		$passform->password('pass_rep', 'Passwort Wiederholung');
		$tabs->add( 'Passwort', $msg.$passform );
	}

	if( $modulrights['rights'] ) {
		$i++;

		if( isset( $_POST['type'] )) {
			$type = 4*$_POST['type']['sysadmin']+2*$_POST['type']['backend']+$_POST['type']['frontend'];
			db()->id_update('user_data', array('type' => $type), $_GET['edit']);
			throw new redirect( $uri.'#tabs-'.$i.'-hash' );
		}

		$typeform = new form_renderer( $uri.'#tabs-'.$i.'-hash' );
		$typeform->checkbox('type[frontend]', 'Frontend', $edituser['type'] & 1 );
		$typeform->checkbox('type[backend]', 'Backend', $edituser['type'] & 2 );
		$typeform->checkbox('type[sysadmin]', 'Sysadmin', $edituser['type'] & 4 );
		$tabs->add( 'Typ', $typeform );

		$i++;
		$rc_groups = new data_controller( 'user_group_owner', $uri.'#tabs-'.$i.'-hash' );
		$rc_groups->add('group', 'Gruppe', 1, 1, 1, 1, 'select', db()->select('user_groups')->relate());
		$rc_groups->add('start_date', 'Start Datum', 1, 1, 1, 0, 'date');
		$rc_groups->add('end_date', 'End Datum', 1, 1, 1, 0, 'date');
		$rc_groups->auto['create'] = array( 'user' => $edituser['id'] );
		$rc_groups->condition = 'user = '.$edituser['id'];
		$rc_groups->pk = $rc_groups->prefix = 'group';
		if( $rc_groups->run()) throw new redirect( $uri.'#tabs-'.$i.'-hash' );
		$tabs->add( 'Rechte', $rc_groups->get_list().'<h4>Gruppe hinzufügen</h4>'.$rc_groups->get_form());
	}

	$grid[1]->box( $form.$tabs, 'User bearbeiten' );
}
