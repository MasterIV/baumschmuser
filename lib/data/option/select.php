<?php

class data_option_select {
	private $info;

	function __construct($info) {
		$this->info = $info;
	}

	public function getOptions() {
		return is_array( $this->info['options'] )
				? $this->info['options']
				: array_map( 'trim', explode(',', $this->info['options']));
	}

	public function getField( $name, $value ) {
		return new form_field_select( $name, $this->info['caption'], $this->getOptions(), $value);
	}
}
