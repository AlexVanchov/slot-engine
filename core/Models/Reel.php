<?php

namespace Core\Models;

/**
 * Class Reel
 * @package Core\Models
 */
class Reel {
	private array $symbols = [];

	/**
	 * Add a symbol to the reel
	 * @param Symbol $symbol
	 * @return void
	 */
	public function addSymbol(Symbol $symbol): void
	{
		$this->symbols[] = $symbol;
	}

	/**
	 * Get the symbols on the reel
	 * @return array
	 */
	public function getSymbols(): array
	{
		return $this->symbols;
	}
}