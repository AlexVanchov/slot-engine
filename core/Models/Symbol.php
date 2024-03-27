<?php

namespace Core\Models;

class Symbol {
	public $id;
	public $type;

	public function __construct($id, $type) {
		$this->id = $id;
		$this->type = $type;
	}
}
