<?php

// Ne Menge geraffel
require 'inc/common.php';
define( 'IV_SELF', 'index.php?' );

// Include Dateien einbinden
foreach( glob('inc/*.index.php') as $f ) include $f;
foreach( glob('inc/*.all.php') as $f ) include $f;

// User ermitteln
if( !$user = $session->user( 1 ))
	$user = $session->relogin( 1 );

// Objekte initialisieren
iv::put( 'user', $user );
iv::put( 'userdata', $userdata = $user ? (array) $user : false );
iv::put( 'rights', $rights = new rights_container($user->id, $user->type & 4));
iv::put( 'loader', $loader = new template_loader( 'style/'.$conf->page->style ));

if( $user ) {
	$profilInfo = iv::get('useroptions');
	$profilInfo['profil'] = array(
		'caption' => 'Profil',
		'items' => db()->user_profil->all()->assocs('name')
	);

	iv::put( 'profil', $profil = new data_options('user_details', $profilInfo, array( 'user' => $user->id ), $conf));
}

try {
	if( empty( $_GET['interface'] )) {
		if( empty( $_GET['path'] )) {
			$arguments = array();
			$page = intval( $_GET['page'] ) ?: $conf->page->startpage;
		} else {
			$arguments = explode( '/', $_GET['path'] );
			$path = strtolower( array_shift( $arguments ));
			$page = $db->query("SELECT id FROM content_layer WHERE name = '%s'", $path)->value() ?: $conf->page->startpage;
		}

		$layer = db()->id_get('content_layer', $page);
		if( $layer['link'] ) throw new redirect( $layer['link'] );

		// Acces rights prüfen
		if(( $layer['status'] == 1 ) || ( $user && $layer['status'] == 2 ) || ( !$user && $layer['status'] == 3 ) || (( $user->type & 4 ) != 4 && $layer['status'] == 4 ))
			throw new redirect(IV_SELF);

		// Page self setzen
		if( $path == strtolower( $layer['name'] )) define('PAGE_SELF', $_SERVER['REDIRECT_URL'].'?' );
		else define('PAGE_SELF', IV_SELF.'page='.$layer['id'].'&' );

		$template = $layer['template'] ? 'layer/'.$layer['template'] : 'layer/index';
		$renderer = new cms_renderer( $layer, iv::get('scripts'));
		$view = new view( $template );

		foreach( $renderer->getLayer( $user ) as $key => $value )
			$view->assign( $key, $value );

		$view->assign( 'options', $conf );
		$view->display();
	} else {
		$interface = $_GET['interface'];

		if( !preg_match('/^[-\w]+(\.[-\w]+)*$/', $interface )
				|| !is_file( 'interfaces/'.$interface.'.php' ))
			throw new Exception('Interface not found!');

		include 'interfaces/'.$interface.'.php';
	}
} catch( redirect $e ) {
	header( 'Location: '.$e->getMessage());
} catch( ErrorException $e ) {
	echo $e->getMessage();
	echo $e->getTraceAsString();
} catch( Exception $e ) {
	echo $e->getMessage();
	echo $e->getTraceAsString();
}
