<?php

class cms_renderer {
	private $scripts;
	private $layer;

	function __construct($layer, $scripts) {
		$this->layer = $layer;
		$this->scripts = $scripts;
	}

	public function getLayer( $user ) {
		$paneldata = $this->loadPanels( $user );
		$panels = $this->groupPanels( $paneldata );
		return $this->getPanelgroups( $panels );
	}

	private function loadPanels( $user ) {
		// Status Activ, Registered Only & Unregistered Only (don't try to understand this)
		$status = $user ? $user->type & 2 ? '0,3,5' : '0,3' : '0,2';

		// Somtimes performance doesn't makes the code more easy
		return db()->query( "SELECT pn.*, cv.name as 'var', cv.value as 'value'
			FROM content_layer node
			JOIN content_layer parent ON node.lft BETWEEN parent.lft AND parent.rgt
			JOIN content_panel pn ON pn.layer = parent.id
			LEFT JOIN content_variable cv ON pn.id = cv.panel
			WHERE node.id = %d AND pn.status IN ( %s )
			UNION SELECT pn.*, cv.name as 'var', cv.value
			FROM content_panel pn
			LEFT JOIN content_variable cv ON pn.id = cv.panel
			WHERE pn.layer is null AND pn.status IN ( %s )
			ORDER BY prio, id", $this->layer['id'], $status, $status );
	}

	private function groupPanels( $paneldata ) {
		$panels = array();

		foreach( $paneldata as $row ) {
			// Vererbung deaktiviert
			if( $row['status'] == 4 && $row['layer'] != $this->layer['id'] ) continue;

			if( empty( $panels[$row['id']] )) {
				$row['vars'] = array( $row['var'] => $row['value'] );
				unset( $row['var'], $row['value'] );
				$panels[$row['id']] = $row;
			} else {
				$panels[$row['id']]['vars'][$row['var']] = $row['value'];
			}
		}

		return $panels;
	}

	private function getPanelgroups( $panels ) {
		$panelgroups = array();

		foreach( $panels as $panel ) {
			$panelgroups[$panel['group']] .= $this->getPanel( $panel, $panel['vars'] );
		}


		return $panelgroups;
	}

	private function getPanel( $panel, $panelvars ) {
		if( isset( $this->scripts[$panel['script']] )) {
			ob_start();
			global $db, $user, $userdata, $session, $conf;
			include 'scripts/'.$this->scripts[$panel['script']]['script'].'.php';
			$panel['content'] = ob_get_clean();
			$panel['options'] = iv::get('conf');

			if( $panel['template'] ) return template('panel/'.$panel['template'])->render( $panel );
			else return $panel['content'];
		}
	}
}
