<?php

namespace Core\Models;

class Payline {
	public $positions = [];

	public function __construct(array $positions) {
		$this->positions = $positions;
	}

	public function getPositions() {
		return $this->positions;
	}

	public function evaluatePayline() {
		$payline = [];
		$payline[] = $this->positions[0];
		$payline[] = $this->positions[1];
		$payline[] = $this->positions[2];
		return $payline;
	}
}