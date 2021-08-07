<?php

$result = $session->changePassword( MODUL_SELF );

if( $result['error'] ) $view->error( $result['error'] );
if( $result['success'] ) $view->success( $result['success'] );

$view->box( $result['form'], 'Passwort ändern', '450px' );
