<?php

$packages = update_package::liste();
$sources = db()->query('SELECT * FROM update_server')->assocs('id');

if( file_exists( 'inc/ftp.config.php' )) {
	require_once 'inc/ftp.config.php';
	$writer = new writer_ftp( $ftp_host, $ftp_user, $ftp_pass, $ftp_dir );
} else {
	$writer = new writer_fs();
}

if(!empty($_POST['server'])) {
	$url = $_POST['server'].'?interface=iv.exchange&plain';

	if(empty($_POST['force'])) {
		try {
			$info =  parse_url($_POST['server']);

			if (!$packages = json_decode(file_get_contents($url), true))
				throw new exception_user('Invalider Server.');
			if ($_SERVER['HTTP_HOST'] == $info['host'] && trim($info['path'], '/') == trim(dirname($_SERVER['DOCUMENT_URI']), '/'))
				throw new exception_user('Bitte nicht den eigenen Server hinzufügen.');
			if (db()->update_server->url($_POST['server']))
				throw new exception_user('Der Server wurde bereits eingetragen.');

		} catch (ErrorException $e) {
			throw new exception_user('Server nicht erreichbar.');
		}
	}

	db()->update_server->insert(array(
		'name' => $_POST['name'],
		'url' => $_POST['server'],
		'forward' => !empty($_POST['forward'])
	));

	throw new redirect( MODUL_SELF );
} elseif( !empty( $_GET['edit'] )) {
	if( empty( $packages[$_GET['edit']] ))
		throw new Exception( 'Paket nicht gefunden!' );
	new update_manager( $view, $packages[$_GET['edit']] );
} elseif( isset( $_GET['storefile'] )) {
	$view->format = 'plain';

	$pkg = new update_package(array(
		'id' => $_POST['pkg'],
		'version' => $_POST['version']
	));

	$pkg->updateFile( $_POST['path'], $_POST['content'] );
	$view->content('ok');
} elseif( isset( $_GET['addpackage'] )) {
	$view->format = 'plain';
	$pkg = new update_package( $_POST );
	$pkg->install( $writer, $sources[$_POST['source']]['forward'] );
	$view->content('ok');
} elseif( isset( $_GET['getmigrations'] )) {
	$all = globFiles('migration/*');
	$applied = db()->select('update_migration')->assocs('id');
	$result = $pending =array();

	foreach( $all as $id )
		if( empty( $applied[$id] ))
			$pending[] = $id;

	$view->plain(json_encode( $pending ));
} elseif( !empty( $_GET['install'] )) {
	$view->format = 'plain';
	$migrations = new update_migration();
	$migrations->install( $_GET['install'] );
} else {
	$view->js('assets/js/update.js');
	$view->js('assets/js/migration.js');

	$form = new form_renderer(MODUL_SELF);
	$form->text('name', 'Bezeichnung');
	$form->text('server', 'Server')->required();
	$form->checkbox('forward', 'Forwarding');
	$form->checkbox('force', 'Erzwingen');

	$view->content(template('iv.packages.install')->render( array(
		'sources' => $sources,
		'packages' => array_values( $packages ),
		'serverform' => $form
	)));
}


