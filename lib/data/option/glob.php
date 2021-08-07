<?php

class data_option_glob {
	private $info;

	function __construct($info) {
		$this->info = $info;
	}

	public function getOptions() {
		return globFiles( $this->info['pattern'] );
	}

	public function getField( $name, $value ) {
		return new form_field_select( $name, $this->info['caption'], $this->getOptions(), $value);
	}
}
