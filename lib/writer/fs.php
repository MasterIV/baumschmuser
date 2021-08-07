<?php

class writer_fs {
	public function put( $path, $content ) {
		$this->dir($path);
		file_put_contents( $path, $content );
		chmod( $path, 0666 );
	}

	public function dir( $path, $mod = 0777 ) {
		$folders = explode( '/', $path );
		$dir = '.';

		while( count( $folders ) > 1 ) {
			$dir .= '/' . array_shift( $folders );
			if( !is_dir( $dir )) {
				mkdir( $dir );
				chmod( $dir, 0777 );
			}
		}
	}

	public function delete( $path ) {
		unlink( $path );
	}
}
