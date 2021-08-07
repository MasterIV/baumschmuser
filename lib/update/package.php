<?php

class update_package {
	public $id;
	public $version;
	public $server;
	public $forward;

	private function packageFileName() {
		return './package/'.$this->id.'.json';
	}

	public static function liste( $ip = null ) {
		$packages = db()->update_package->all()->assocs('id');

		foreach( glob('package/*') as $f )
			if( is_array( $pack = json_decode(file_get_contents($f), 1))) {
				$id = substr( $f, 8, -5 );

				$package = array(
					'id' => $id,
					'version' => 0,
					'name' => $pack['name'],
					'description' => $pack['description'],
					'autor' => $pack['autor'],
				);

				if( isset( $packages[$id] )) {
					$packages[$id] = array_merge( $package, $packages[$id] );
				} else {
					db()->update_package->insert( $package );
					$packages[$id] = $package;
				}
			}

		if( $ip ) {
			return array_intersect_key( $packages, db()->query("SELECT DISTINCT up.id
				FROM update_package up JOIN update_share us ON up.id = us.package
				WHERE up.version > 0 AND '%s' LIKE REPLACE( us.pattern, '*', '%%' )", $ip )->relate('id', 'id'));
		} else {
			return $packages;
		}
	}

	public function __construct( $data ) {
		$this->id = $data['id'];
		$this->version = $data['version'];
		$this->server = $data['source'];
		$this->forward = $data['forward'];
	}

	public function addFile( $file ) {
		db()->update_file->insert( array(
			'package' => $this->id,
			'path' => $file,
			'hash' => self::hash( $file ),
			'version' => $this->version + 1,
			'content' => base64_encode(file_get_contents( $file ))
		), 'REPLACE');
	}

	public function publish() {
		if( $this->server ) throw new Exception('Invalid Package');

		$files = db()->query("SELECT path, hash FROM update_file
			WHERE version <= %d and package = '%s'", $this->version, $this->id );

		foreach( $files as $file ) {
			$hash = self::hash( $file['path'] );

			if( $file['hash'] != $hash )
				$this->addFile( $file['path'] );
		}

		$this->addFile($this->packageFileName());

		$this->version++;
		db()->query("UPDATE update_package SET version = %d
			WHERE id = '%s'", $this->version, $this->id );
	}

	public function deliver( $ip, $current = 0 ) {
		if(( $this->server && empty($this->forward)) || $this->version < 1 )
			throw new Exception('Invalid Package (Version/Source)');

		$shares = db()->query( "SELECT count(*) FROM update_share
			 WHERE package = '%s' AND '%s' LIKE REPLACE( pattern, '*', '%%' )",
			$this->id, $ip )->value();
		if( !$shares ) throw new Exception('Invalid Package (Share)');

		return array(
			'id' => $this->id,
			'version' => $this->version,

			'dependencies'  => db()->query( "SELECT required, version
					FROM update_dependency WHERE package = '%s'", $this->id )->assocs(),

			'files' => db()->query( "SELECT path, version, content
					FROM update_file WHERE package = '%s' AND version BETWEEN %d AND %d",
				$this->id, $current+1, $this->version )->assocs(),
		);
	}

	public function updateFile( $path, $content ) {
		db()->update_file->insert( array(
			'package' => $this->id,
			'path' => $path,
			'version' => $this->version,
			'content' => $content ), 'REPLACE');
	}

	public function install( $writer, $forward = false ) {
		// Write files to disc and clean db
		$files = db()->query("SELECT path, content FROM update_file WHERE package = '%s' AND content IS NOT NULL", $this->id );
		foreach( $files as $file ) $writer->put( $file['path'], base64_decode( $file['content'] ));

		// Remove files from db
		if(!$this->forward) {
			db()->query("UPDATE update_file SET content = NULL WHERE package = '%s'", $this->id);
			db()->query("OPTIMIZE TABLE `update_file` ");
		}

		// Ensure stuff, you know
		$packageInfo = json_decode(file_get_contents( $this->packageFileName()), true );

		if( isset( $packageInfo['directories'] ))
			foreach( $packageInfo['directories'] as $dir => $mod )
				$writer->dir( $dir, $mod );

		if( class_exists('iv')) iv::rebuildCache();

		db()->update_package->insert(array(
			'id' => $this->id,
			'version' => $this->version,
			'source' => $this->server,
			'forward' => $forward
		), 'REPLACE' );
	}

	public function changeLog($comment) {
		db()->update_change_log->insert(array(
			'package' => $this->id,
			'comment' => $comment
		));
	}

	public static function hash( $file ) {
		return md5(file_get_contents($file));
	}
}
