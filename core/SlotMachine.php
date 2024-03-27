<?php

namespace Core;

use Core\Models\Payline;
use Core\Models\Reel;
use Core\Models\Symbol;

class SlotMachine
{
	private $configLoader;
	private $reels = [];
	private $paylines = [];
	private $tiles = [];
	private $pays = [];
	private $stake;

	public function __construct()
	{
		$appContainer = AppContainer::getInstance();
		$this->configLoader = $appContainer->get('configLoader');
		$this->loadConfiguration();
	}

	private function loadConfiguration(): void
	{
		$config = $this->configLoader->getConfig();

		// Load symbols (tiles)
		foreach ($config['tiles'] as $tileConfig) {
			$this->tiles[$tileConfig['id']] = new Symbol($tileConfig['id'], $tileConfig['type']);
		}

		// Load reels
		foreach ($config['reels'][0] as $reelConfig) {
			$reel = new Reel();
			foreach ($reelConfig as $symbolId) {
				$reel->addSymbol($this->tiles[$symbolId]);
			}
			$this->reels[] = $reel;
		}

		// Load paylines
		foreach ($config['lines'] as $lineConfig) {
			$this->paylines[] = new Payline($lineConfig);
		}

		// Load pays
		$this->pays = $config['pays'];

		// Load special symbols

	}

	public function spin($stake)
	{
		$this->stake = $stake;
		$screen = []; // This will hold the 5x3 grid of symbols
		$details = []; // Use this to store details on any symbols that have been converted

		foreach ($this->reels as $reelIndex => $reel) {
			$symbols = $reel->getSymbols();
			$reelPosition = rand(0, count($symbols) - 1); // Random starting position for this reel

			for ($col = 0; $col < 3; $col++) {
				$position = ($reelPosition + $col) % count($symbols); // Wrap around if necessary
				$screen[$reelIndex][$col] = $symbols[$position];
			}
		}
//		$screen = array(
//			0 => array(
//				0 => new Symbol(7, 'normal'),
//				1 => new Symbol(7, 'normal'),
//				2 => new Symbol(7, 'normal',)),
//			1 => array(
//				0 => new Symbol(7, 'normal',),
//				1 => new Symbol(7, 'normal',),
//				2 => new Symbol(7, 'normal',),),
//			2 => array(
//				0 => new Symbol(7, 'normal',),
//				1 => new Symbol(7, 'normal',),
//				2 => new Symbol(4, 'normal',)),
//			3 => array(
//				0 => new Symbol(7, 'normal',),
//				1 => new Symbol(9, 'normal',),
//				2 => new Symbol(9, 'normal',)),
//			4 => array(
//				0 => new Symbol(10, 'mystery'),
//				1 => new Symbol(8, 'normal',),
//				2 => new Symbol(8, 'normal',)));

		// Here, implement logic to check for and convert special symbols
		// For simplicity, this step is skipped in this example

		// Implement logic to evaluate paylines based on the screen and configuration
		$paylines = $this->evaluatePaylines($screen);

		return [
			'screen' => $screen, // The 5x3 grid of symbols
			'details' => $details, // Details on any conversions that took place
			'paylines' => $paylines, // Information on paylines that have won
		];
	}

	public function evaluatePaylines($screenOriginal): array
	{
		$screen = array_map(function($reel) {
			return array_map(function($symbol) {
				// Assuming Symbol is a class with an id and a type. Adjust cloning if your structure is different
				return clone $symbol;
			}, $reel);
		}, $screenOriginal);


		$wins = [];
		// Special symbol conversion, assuming ID 10 is the special symbol
		$conversionTarget = null;
		foreach ($screen as $reel) {
			foreach ($reel as $symbol) {
				if ($symbol->id == 10 && !$conversionTarget) { // Find the first symbol for conversion
					continue;
				} elseif ($conversionTarget === null) {
					$conversionTarget = $symbol->id; // Set conversion target to the first normal symbol found
					break;
				}
			}
		}

		foreach ($screen as $reelIndex => $reel) {
			foreach ($reel as $symbol) {
				if ($symbol->id == 10) { // Convert special symbols
					$symbol->id = $conversionTarget;
					echo "Special symbol(10) converted to $conversionTarget" . "<br>";
				}
			}
		}

		// Evaluate each payline
		foreach ($this->paylines as $line) {
			$prevSymbolId = null;
			$matchCount = 0;
			$matches = [];

			foreach ($line->getPositions() as $position => $row) {
				$currentSymbol = $screen[$position][$row];
				if ($prevSymbolId === null || $currentSymbol->id === $prevSymbolId) {
					$matchCount++;
					$matches[] = $currentSymbol->id;
				} else {
					if (count(array_unique($matches)) === 1 && $matchCount >= 3) {
						$moneyWon = $this->lookupPayout($matches, $matchCount);
						$wins[] = [
							'line' => $line,
							'matches' => $matches,
							'count' => $matchCount,
							'moneyWon' => $moneyWon,
						];
					}
					// Reset for the next sequence of matches
					$matches = [$currentSymbol->id];
					$matchCount = 1;
				}
				$prevSymbolId = $currentSymbol->id;
			}

			// Check if the last sequence of symbols forms a win
			$moneyWon = $this->lookupPayout($matches, $matchCount);
			if (count(array_unique($matches)) === 1 && $matchCount >= 3) {
				$wins[] = [
					'line' => $line,
					'matches' => $matches,
					'count' => $matchCount,
					'moneyWon' => $moneyWon,
				];
			}
		}

		return $wins;
	}

	private function lookupPayout($matches, $matchCount) {
		$symbolId = $matches[0]; // All matches will be the same symbol
		foreach ($this->pays as $pay) {
			if ($pay[0] == $symbolId && $pay[1] == $matchCount) {
				// calculate the payout amount based on the stake
				return $pay[2] * $this->stake;// Return the payout amount
			}
		}

		return 0; // No payout for this sequence
	}

}
