<?php

class data_option_sql {
	private $info;

	function __construct($info) {
		$this->info = $info;
	}

	public function getOptions() {
		return db()->query($this->info['sql'])->relate();
	}

	public function getField( $name, $value ) {
		return new form_field_select( $name, $this->info['caption'], $this->getOptions(), $value);
	}
}
