<?php

class writer_ftp {
	private $ftp;

	public function __construct( $host, $user, $pass, $dir ) {
		if( !$this->ftp = ftp_connect($host))
			throw new Exception( 'Could not Connect do FTP Host' );

		ftp_login( $this->ftp, $user, $pass );
		ftp_chdir( $this->ftp, $dir );
	}

	public function put( $path, $content ) {

	}
}
