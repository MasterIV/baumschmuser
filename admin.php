<?php

$start = microtime(true);

// Ne Menge geraffel
require 'inc/common.php';
define( 'IV_SELF', 'admin.php?' );

// Include Dateien einbinden
foreach( glob('inc/*.admin.php') as $f ) include $f;
foreach( glob('inc/*.all.php') as $f ) include $f;

// User ermitteln
if( !$user = $session->relogin( 2 ))
	$user = $session->user( 2 );

// Tempolate engine Initialisieren
$options = iv::get('conf');
iv::put( 'loader', $loader = new template_loader( 'theme/'.$options->system->theme ));

if( $user ) {
	$profilInfo = iv::get('useroptions');
	$profilInfo['profil'] = array(
		'caption' => 'Profil',
		'items' => db()->user_profil->all()->assocs('name')
	);

	// Userinfos publizieren
	iv::put( 'user', $user );
	iv::put( 'userdata', $userdata = (array) $user );
	iv::put( 'rights', $rights = new rights_container($user->id, $user->type & 4));
	iv::put( 'profil', $profil = new data_options('user_details', $profilInfo, array( 'user' => $user->id ), $conf));

	$view = new view( 'admin' );
	$modul = $_GET['modul'];

	if( !preg_match('/^[-\w]+(\.[-\w]+)*$/', $modul )
					|| !is_file( 'moduls/'.$modul.'.php' )
					|| !$rights->has( 'modul', $modul ))
		$modul =  'iv.nav';

	define( 'MODUL_SELF', IV_SELF.'modul='.$modul );

	// Start Output Buffer
	ob_start();

	$assignment = array_map( 'intval', $db->base_menu_point->get('user = %d', $user->id)->relate('category','modul'));
	$menu = array( array( 'name' => 'Allgemein', 'children' => array()));
	foreach( $db->base_menu_category->get('user = %d', $user->id) as $cat )
		$menu[$cat['id']] = array( 'name' => $cat['name'], 'children' => array());

	foreach( iv::get('moduls') as $point )
		if( $rights->has('modul', $point['file'] ))
			if( isset( $assignment[$point['file']] )) {
				$cat = $assignment[$point['file']];
				if( $cat ) $menu[$cat]['children'][] = $point;
			} else {
				$menu[0]['children'][] = $point;
			}

	try {
		$modulrights = $rights->flags( 'modul', $modul );
		$view->assign('menu', $menu);
		include( 'moduls/'.$modul.'.php' );
		$view->content( ob_get_clean());
		$view->display();
	} catch( redirect $e ) {
		ob_end_clean();
		header( 'Location: '.$e->getMessage());
	} catch( ErrorException $e ) {
		ob_end_clean();
		echo $e->getMessage();
		echo $e->getTraceAsString();
	} catch( exception_user $e ) {
		ob_end_clean();
		$view->error($e->getMessage());
		$view->content('<p align="center"><button class="btn" onclick="window.history.back()">Zurück</button></p>');
		$view->display();
	} catch( Exception $e ) {
		ob_end_clean();
		$view->error( $e->getMessage().PHP_EOL.$e->getTraceAsString());
		$view->display();
	}
} else {
	$view = new view('login');
	$view->assign( 'submit_url', IV_SELF );

	if( isset( $_POST['admin_name'] ))
		if( !$session->login( $_POST['admin_name'], $_POST['admin_pass'],  $_POST['relogin'], 2 )) {
			$view->error( 'Userdaten ungültig!' );
		} else {
			header( 'LOCATION: admin.php' );
			exit();
		}

	$view->display();
}

$gentime = (microtime(true)-$start);

//echo '<pre>';
//echo $db->host_info."\n";
//print_r( $gentime );
