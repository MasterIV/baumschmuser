<?php

if( isset( $_POST['source'] ))
	$panelvars['content'] = $_POST['source'];

$editor = $panel['script'] == 'php'
	? $profil->system->php_editor
	: $profil->system->html_editor;

$view->box(template('editor/'.$editor)->render(array(
	'action' => EDITOR_SELF,
	'cancel' => LAYER_SELF,
	'syntax' => $panel['script'],
	'content' => $panelvars['content']
)), 'Panel bearbeiten: '.$panel['name'] );
