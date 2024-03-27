<?php

namespace Core\Models;

class Reel {
	public $symbols = [];

	public function addSymbol(Symbol $symbol) {
		$this->symbols[] = $symbol;
	}

	public function getSymbols() {
		return $this->symbols;
	}
}