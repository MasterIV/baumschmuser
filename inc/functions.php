<?php

function escape_gpc_value(&$value) {
	$value = db()->escape($value);
}
function escape_gpc() {
	array_walk_recursive( $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST), 'sql_escape_value');
}

function alert( $message, $type = 'error' ) {
	return '<div class="alert alert-'.$type.'"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$message.'</div>';
}

function literal( $value ) {
	return is_array( $value ) ? $value[0] : $value;
}

function globFiles( $pattern, $extension = true ) {
	$glob = glob( $pattern );

	if( !empty( $glob ))
		foreach(  $glob as $file )
			if( $extension ) $result[] = substr( $file, 1+strrpos( $file, '/' ));
			else $result[] = substr( $file, 1+strrpos( $file, '/' ), -1*strlen(strrchr( $file, '.')));

	return $result ? array_combine($result, $result) : array();
}

function showMenu($points) {
	echo '<div class="button-group">';

	foreach ($points as $p) {
		if($GLOBALS['layer']['id'] == $p['layer'])
			echo '<a class="active" href="index.php?page='.$p['layer'].'">'.$p['label'].'</a>';
		else
			echo '<a href="index.php?page='.$p['layer'].'">'.$p['label'].'</a>';
	}

	echo '</div>';
}
