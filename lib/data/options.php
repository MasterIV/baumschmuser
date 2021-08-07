<?php

class data_options {
	/** @var \mysql_table */
	private $table;
	/** @var array */
	private $info;
	/** @var array */
	private $auto;

	function __construct($table, $info, $auto = array(), $defaults = null) {
		$this->info = $info;
		$this->table = db()->t($table);
		$this->auto = $auto;

		foreach( $this->info as $cat => $opts ) {
			if( isset( $this->{$cat} ))
				throw new Exception('Invalid Category Name: '.$cat);

			$this->{$cat} = new stdClass();
			foreach( $opts['items'] as $id => $option )
				$this->{$cat}->{$id} = isset($defaults->{$cat}->{$id}) && empty($option['value'])
					? $defaults->{$cat}->{$id}
					: $option['value'];
		}

		if( $auto ) {
			$query = array();
			foreach( $auto as $k => $v ) $query[] = db()->format("`%s` = '%s'", $k, $v );
			$res = $this->table->get( implode( ' AND ', $query ));
		} else {
			$res = $this->table->all();
		}

		foreach( $res as $value )
			if( isset( $this->{$value['category']}->{$value['name']} ))
				$this->{$value['category']}->{$value['name']} = $value['value'];
	}

	public function saveData() {
		$query = array();

		foreach( $_POST['options'] as $cat => $options )
			foreach( $options as $key => $value )
				if( isset( $this->{$cat}->{$key} ) && $this->{$cat}->{$key} != $value )
					$query[] = array_merge( $this->auto, array(
						'category' => $cat,
						'name' => $key,
						'value' => $value,
					));

		$this->table->multiInsert( $query, 'REPLACE' );
	}

	public function set($cat, $key, $value) {
		$this->table->insert(array(
				'category' => $cat,
				'name' => $key,
				'value' => $value,
		), 'REPLACE');
	}

	public function getForm( $action ) {
		$form = new form_renderer($action);
		$form->append($tabs = new widget_tabs());

		foreach( $this->info as $cat => $category ) {
			$inputs = array();

			foreach( $category['items'] as $id => $field ) {
				$name = "options[{$cat}][{$id}]";
				$value = $this->{$cat}->{$id};
				$class = 'data_option_'.$field['type'];

				if( class_exists( $class )) {
					$provider = new $class( $field );
					$inputs[] = $provider->getField( $name, $value );
				} elseif( $field['type'] == 'text' ) {
					$inputs[] = new form_field_text( $name, $field['caption'], $value );
				} elseif( $field['type'] == 'textarea' ) {
					$inputs[] = new form_field_textarea( $name, $field['caption'], $value );
				} elseif( $field['type'] == 'checkbox' ) {
					$inputs[] = new form_field_checkbox( $name, $field['caption'], $value );
				}
			}

			$tabs->add(
				literal( $category['caption'] ),
				implode( '', $inputs )
			);
		}

		return $form;
	}
}
