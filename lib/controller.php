<?php

abstract class controller {
	protected $view;

	public abstract function index();

	public function __construct( view $view ) {
		$this->view = $view;
		$action = empty( $_GET['action'] ) ? 'index' : trim( $_GET['action'] );
		if( !method_exists( $this, $action )) $action = 'index';
		if( !defined( 'ACTION_SELF' )) define( 'ACTION_SELF', MODUL_SELF.'&action='.$action );
		$this->view->content( $this->$action());
	}
}
