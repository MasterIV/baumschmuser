<?php

/**
	* Erweiterte Umsetzung des Registry-Pattern
	*/
class iv {
	private static $data = array();
	private static $packages = array();

	public static function init() {
		if(is_file('cache/system.php')) {
			self::$data = include 'cache/system.php';
		} else {
			self::$data = self::rebuildCache();
		}
	}

	public static function rebuildCache() {
		$data = array();

		foreach( glob('package/*') as $f )
			if( is_array( $pack = json_decode(file_get_contents($f), 1))) {
				$data = array_merge_recursive($data, $pack );
				self::$packages[substr( $f, 8, -5 )] = 1;
			}

		file_put_contents('cache/system.php', "<?php\n\nreturn ".var_export($data, true).";");
		return $data;
	}

	public static function pack( $id ) {
		return isset( self::$packages[$id] );
	}

	public static function get( $var ) {
		if( !isset( self::$data[$var] )) return null;
		else return self::$data[$var];
	}

	public static function put( $var, $value ) {
		self::$data[$var] = $value;
	}
}

function iv( $var, $val = null ) {
	if( $val ) iv::set( $var, $val );
	else return iv::get( $var );
}

/**
	* Get DB Connection
	* @return mysql_connection
	*/
function db() {
	return iv::get( 'db' );
}

/**
	* Get Current User
	* @return mixed
	*/
function user() {
	return iv::get( 'user' );
}

/**
	* Returns the id of current user
	* @return int
	*/
function current_user() {
	return iv::get( 'user' )->id;
}
