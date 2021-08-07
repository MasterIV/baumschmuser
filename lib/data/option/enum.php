<?php

class data_option_enum {
	private $info;

	function __construct($info) {
		$this->info = $info;
	}

	public function getOptions() {
		return iv::get($this->info['options']);
	}

	public function getField( $name, $value ) {
		return new form_field_select( $name, $this->info['caption'], $this->getOptions(), $value);
	}
}
