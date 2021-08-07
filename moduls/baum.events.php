<?php

$rc =  new data_controller( 'raid_events', MODUL_SELF );
$rc->auto['create'] = ['leader' => current_user()];

$rc->add( 'id', 'Id', 1, 0, 0, 0 );
$rc->add( 'destination', 'Instanz', 1, 1, 1, 0, 'select', $raids );
$rc->add( 'start', 'Datum', 1, 1, 1, 1, 'date' );
$rc->add( 'zeit', 'Uhrzeit', 0, 1, 1, 1 );
$rc->add( 'comment', 'Kommentar', 0, 1, 1, 0, 'textarea' );
$rc->add( 'leader', 'Leiter', 0, 0, 1, 0, 'select', db()->user_data->all()->relate());

//$rc->add( 'tanks', 'Tanks', 0, 1, 1, 1 );
//$rc->add( 'heal', 'Heiler', 0, 1, 1, 1 );
//$rc->add( 'dps', 'Schaden', 0, 1, 1, 1 );

$rc->run();

$view->box($rc->get_list(), 'Raids');
$view->box($rc->get_form(), isset($_GET['edit']) ? 'Raid bearbeiten' : 'Raid erstellen');
