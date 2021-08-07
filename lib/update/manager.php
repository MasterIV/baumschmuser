<?php

class update_manager  extends controller {
	private $package;

	public function __construct(view $view, $package ) {
		$this->package = $package;
		parent::__construct($view);
	}

	public function publish() {
		$pkg = new update_package( $this->package );
		$pkg->publish();
		throw new redirect( MODUL_SELF.'&edit='.$pkg->id );
	}

	public function add_files() {
		$this->view->format = 'plain';
		return template('iv.packages.addfiles')->render(array(
			'package' => $this->package,
			'files' => self::get_files()
		));
	}

	public function update_files( $redirect ) {
		$pkg = new update_package( $this->package );

		foreach( $_POST['files'] as $file )
			if( file_exists( $file ))
				$pkg->addFile( $file );

		throw new redirect( MODUL_SELF.'&edit='.$pkg->id.'#tabs-2-hash'  );
	}

	public function index() {
		$pack = $this->package;
		$self = MODUL_SELF.'&edit='.$pack['id'];
		$cond = db()->format( "package = '%s'", $pack['id'] );

		$rcf = new data_controller( 'update_file', $self.'#tabs-2-hash' );
		$rcf->add( 'path', 'Datei-Pfad' );
		$rcf->add( 'version', 'Version' );
		$rcf->edit = $rcf->create = false;
		$rcf->condition  = $cond;
		$rcf->prefix = 'file_';
		$rcf->pk = 'path';

		$plink = MODUL_SELF.'&action=add_files&edit='.$pack['id'];
		$addbtn = '<p align="center"><input type="button" class="btn btn-primary" '
				.'value="Dateien hinzufügen" onclick="popup( \''.$plink.'\', 800 )"></p>';

		if( $rcf->run()) throw new redirect( $self.'#tabs-2-hash' );

		$rcs = new data_controller( 'update_share', $self.'#tabs-3-hash' );
		$rcs->add( 'comment', 'Kommentar', 1, 1, 1, 1 );
		$rcs->add( 'pattern', 'Freigabe', 1, 1, 1, 1 );
		$rcs->condition  = $cond;
		$rcs->auto['create'] = array( 'package' => $pack['id'] );
		$rcs->prefix = 'share_';

		if( $rcs->run()) throw new redirect( $self.'#tabs-3-hash' );

		$packages = $GLOBALS['packages'];
		unset($packages[$pack['id']]);
		$depend = array_keys( $packages ) ;

		$rcd = new data_controller( 'update_dependency', $self.'#tabs-4-hash' );
		$rcd->add( 'required', 'Benötigtes Paket', 1, 1, 1, 1, 'select', $depend );
		$rcd->add( 'version', 'Version', 1, 1, 1, 1 );
		$rcd->condition  = $cond;
		$rcd->auto['create'] = array( 'package' => $pack['id'] );
		$rcd->prefix = 'depend_';

		if( $rcd->run()) throw new redirect( $self.'#tabs-4-hash' );

		$tabs = new widget_tabs( 'update_files', $self );

		$tabs->add( 'Infos', template('iv.packages.info')->render( $pack ));
		$tabs->add( 'Dateien', $rcf->get_list( 25 ).$addbtn );
		$tabs->add( 'Freigaben', $rcs->get_form().$rcs->get_list());
		$tabs->add( 'Abhängigkeiten', $rcd->get_form().$rcd->get_list());

		$this->view->box( $tabs, 'Paket bearbeiten', '600px' );
	}

	/**
	 * @return array
	 */
	public static function get_files() {
		$blacklist = array_map('trim', file('.ivignore'));
		$files = db()->select('update_file')->relate('path', 'path');
		$possible = array();

		$iterate = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
		foreach ($iterate as $file)
			if ($file->isFile()) {
				$fileName = str_replace('\\', '/', $file);
				$skip = isset($files[$fileName]);

				foreach ($blacklist as $bl)
					if (strpos($fileName, $bl) > -1) $skip = true;
				if (!$skip)
					$possible[] = $fileName;
			}

		return $possible;
	}

	public static function get_assignable() {
		$auto = array();

		foreach( glob('package/*') as $f )
			if( is_array( $pack = json_decode(file_get_contents($f), 1))) {
				$id = substr( $f, 8, -5 );

				if($pack['moduls'])
					foreach ($pack['moduls'] as $m) {
						$auto["./assets/small/{$m['icon']}"] = $id;
						$auto["./assets/large/{$m['icon']}"] = $id;
						$auto["./moduls/{$m['file']}.php"] = $id;
					}

				if($pack['scripts'])
					foreach($pack['scripts'] as $s) {
						$auto["./scripts/{$s['script']}.php"] = $id;
						if(!empty($s['editor']))
							$auto["./scripts/{$s['editor']}.php"] = $id;
					}
			}

		return $auto;
	}
}
