<?php

class data_controller {
	/** @var \mysql_table */
	public $table;
	public $columns;
	public $condition = 1;
	public $pk = 'id';

	public $delete = true;
	public $edit = true;
	public $create = true;

	public $options = array();
	public $auto = array( 'create' => array(), 'edit' => array() );

	public $prefix = '';
	public $link;

	public static $icon_dir = 'assets/small/';
	public static $icon_edit = 'edit.png';
	public static $icon_delete = 'delete.png';


	public function __construct( $table, $link, $prefix = '' ) {
		$this->link = $link instanceof html_link ? $link : new html_link( $link );
		$this->table = db()->t( $table );
		$this->prefix = $prefix;
	}

	/**
	 * Adds info about a column
	 * @param string $name
	 * @param string $caption
	 * @param boolean $list
	 * @param boolean $create
	 * @param boolean $edit
	 * @param boolean $required
	 * @param string $type
	 * @param array $values
	 */
	public function add( $name, $caption, $list = true, $create = true, $edit = true, $required = false, $type = 'text', $values = array()) {
		$this->columns[] = array(
			'name' => $name,
			'caption' => $caption,
			'list'     => $list,
			'create'   => $create,
			'edit'     => $edit,
			'required' => $required,
			'type' => $type,
			'values' => $values );
	}

	public function option( $icon, $param, $description, $link = NULL, $ask = false ) {
		$this->options[] = array(
				'icon' => $icon,
				'param' => $param,
				'link' => $link ? $link : $this->link,
				'description' => $description,
				'ask' => $ask );
	}

	protected function validate( $type ) {
		$query = $this->auto[$type];

		foreach( $this->columns as $c )
			if( $c[$type] ) {
				if( $c['required'] && empty( $_POST[$c['name']] ))
					throw new exception_user( 'Es wurden nicht alle benötigten Felder ausgefüllt: '.$c['caption'] );

				if( $c['type'] == 'checkbox' )
					$query[$c['name']] = (boolean) $_POST[$c['name']];
				else
					$query[$c['name']] = $_POST[$c['name']];
			}

		return $query;
	}

	protected function link( $key, $val = '' ) {
		return $this->link->pure( array( $this->prefix.$key => $val ));
	}

	public function get_create() {
		if( !$this->create ) throw new exception_user( 'Erzeugen nicht möglich!' );
		return $this->create_form( 'create', $this->link( 'create' ), $_POST );
	}

	public function get_edit( $id ) {
		$cond = $this->condition . db()->format( " AND `{$this->pk}` = %d LIMIT 1", $id );
		if( !$this->edit ) throw new exception_user( 'Bearbeiten nicht möglich!' );
		if( !$edit = $this->table->get( $cond )->assoc()) throw new Exception( 'Datensatz nicht gefunden!' );
		return $this->create_form( 'edit', $this->link( 'update', $edit[$this->pk] ), $edit, $this->link->pure());
	}

	public function get_form() {
		if( empty( $_GET[$this->prefix.'edit'] )) return $this->get_create();
		else return $this->get_edit( $_GET[$this->prefix.'edit'] );
	}

	protected function create_form( $type, $action, $edit, $back = NULL ) {
		$form = new form_renderer( $action, 'Speichern' );
		if( $back ) $form->linkbutton( 'Zurück', $back );

		foreach( $this->columns as $c )
			if ($c[$type]) {
				switch ($c['type']) {
					case 'password':
						$input = $form->password($c['name'], $c['caption'], $edit[$c['name']]);
						break;
					case 'radio':
						$input = $form->radio($c['name'], $c['caption'], $c['values'], $edit[$c['name']]);
						break;
					case 'hidden':
						$input = $form->hidden($c['name'], $edit[$c['name']]);
						break;
					case 'textarea':
						$input = $form->textarea($c['name'], $c['caption'], $edit[$c['name']]);
						break;
					case 'select':
						$input = $form->select($c['name'], $c['caption'], $c['values'], $edit[$c['name']]);
						break;
					case 'checkbox':
						$input = $form->checkbox($c['name'], $c['caption'], $edit[$c['name']]);
						break;
					case 'date':
						$input = $form->field(new form_field_date($c['name'], $c['caption'], $edit[$c['name']]));
						break;
					default:
						$input = $form->text($c['name'], $c['caption'], $edit[$c['name']]);
				}

				if($c['required'])
					$input->required();
			}

		return $form;
	}

	public function get_list( $pagesize = 50 ) {
		$liste = new list_sql( $this->link, $this->prefix, $pagesize );
		$liste->width = '100%';

		foreach ( $this->columns as $c )
			if( $c['list'] )
				switch( $c['type'] ) {
					case 'select':
					case 'radio':
						$liste->assoc( $c['caption'], $c['name'], $c['values'] );
						break;
					case 'date':
						$liste->date( $c['caption'], $c['name'], 'd.m.Y' );
						break;
					case 'time':
						$liste->date( $c['caption'], $c['name'], 'H:i' );
						break;
					default:
						$liste->text( $c['caption'], $c['name'] );
				}

		$options = $liste->add( new list_column_actions( 'Aktionen', $this->pk ));

		foreach( $this->options as $o )
			$options->add( $o['link'], $o['param'], $o['description'], $o['icon'], $o['ask'] );

		if( $this->edit ) $options->add( $this->link, $this->prefix.'edit', "Bearbeiten", self::$icon_dir.self::$icon_edit, false );
		if( $this->delete  ) $options->add( $this->link, $this->prefix.'delete', "Löschen", self::$icon_dir.self::$icon_delete, true );

		return new list_wrapper( $liste, "SELECT * FROM {$this->table} WHERE {$this->condition}" );
	}

	public function run() {
		if( !empty( $_GET[$this->prefix.'delete'] ) && $this->delete ) {
			$cond = $this->condition . db()->format( " AND `{$this->pk}` = '%s'", $_GET[$this->prefix.'delete'] );
			$this->table->del( $cond );
			return true;
		}

		if( !empty( $_GET[$this->prefix.'update'] ) && $this->edit ) {
			$cond = $this->condition . db()->format( " AND `{$this->pk}` = '%s'", $_GET[$this->prefix.'update'] );
			$this->table->update( $this->validate( 'edit' ), $cond );
			return true;
		}

		if( isset( $_GET[$this->prefix.'create'] ) && $this->create ) {
			$this->table->insert( $this->validate( 'create' ));
			return true;
		}

		return false;
	}
}

class list_wrapper {
	/** @var list_sql */
	public $list;
	/** @var string */
	public $query;

	public function __construct( $list, $query ) {
		$this->list = $list;
		$this->query = $query;
	}

	public function __toString() {
		return $this->list->get( $this->query );
	}
}
