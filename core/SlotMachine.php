<?php

namespace Core;

use Core\Models\Payline;
use Core\Models\Reel;
use Core\Models\Symbol;
use Core\Services\ConfigLoader;

class SlotMachine
{
    private ConfigLoader $configLoader;
    private array $reels = [];
    private array $lines = [];
    private array $tiles = [];
    private array $pays = [];
    private float $stake;

    /**
     * @var array Use this to store details on any symbols that have been converted
     */
    private array $details = [];
    private array $screen = [];
    private array $paylines_result = [];

    public function __construct()
    {
        $appContainer = AppContainer::getInstance();
        $this->configLoader = $appContainer->get('configLoader');
        $this->loadConfiguration();
    }

    /**
     * Load the configuration from the JSON file
     * @return void
     */
    private function loadConfiguration(): void
    {
        // Load symbols (tiles)
        foreach ($this->configLoader->getTiles() as $tileConfig) {
            $this->tiles[$tileConfig['id']] = new Symbol(
                $tileConfig['id'],
                $tileConfig['type']
            );
        }

        // Load reels
        foreach ($this->configLoader->getReels()[0] as $reelConfig) {
            $reel = new Reel();
            foreach ($reelConfig as $symbolId) {
                $reel->addSymbol($this->tiles[$symbolId]);
            }
            $this->reels[] = $reel;
        }

        // Load paylines
        foreach ($this->configLoader->getLines() as $lineConfig) {
            $this->lines[] = new Payline($lineConfig);
        }

        // Load pays
        $this->pays = $this->configLoader->getPays();
    }

    /**
     * @param $stake
     * @return array
     */
    public function spin($stake): array
    {
        $this->stake = floatval($stake);
        $this->setRandomScreen();
        $this->evaluatePaylines();

        return [
            'screen' => $this->screen, // The 5x3 grid of symbols
            'details' => $this->details, // Details on any conversions that took place
            'paylines' => $this->paylines_result, // Information on paylines that have won
        ];
    }

    /**
     * Generate a 5x3 grid of symbols
     * @return void
     */
    private function setRandomScreen(): void
    {
        $screen = []; // This will hold the 5x3 grid of symbols

        foreach ($this->reels as $reelIndex => $reel) {
            $symbols = $reel->getSymbols();
            $reelPosition = rand(0, count($symbols) - 1); // Random starting position for this reel

            for ($col = 0; $col < 3; $col++) {
                $position = ($reelPosition + $col) % count($symbols); // Wrap around if necessary
                $screen[$reelIndex][$col] = $symbols[$position];
            }
        }

        $this->screen = $screen;
    }

    /**
     * Evaluate the paylines on the screen and return array of wins
     * @return void
     */
    private function evaluatePaylines(): void
    {
        $wins = [];
        $this->details = $this->handleSpecialSymbols();

        /**
         * @var Payline $line
         */
        foreach ($this->lines as $line) {
            $prevSymbolId = null;
            $matchCount = 0;
            $matches = [];

            $positions = $line->getPositions();
            foreach ($positions as $position => $row) {
                $currentSymbol = $this->screen[$position][$row];
                if ($prevSymbolId === null || $currentSymbol->id === $prevSymbolId) {
                    $matchCount++;
                    $matches[] = $currentSymbol->id;
                } else {
                    if (count(array_unique($matches)) === 1 && $matchCount >= 3) {
                        $wins[] = [
                            'line' => $positions,
                            'matches' => $matches,
                            'count' => $matchCount,
                            'moneyWon' => $this->lookupPayout($matches, $matchCount),
                        ];
                    }
                    // Reset for the next sequence of matches
                    $matches = [$currentSymbol->id];
                    $matchCount = 1;
                }
                $prevSymbolId = $currentSymbol->id;
            }

            // Check if the last sequence of symbols forms a win
            if (count(array_unique($matches)) === 1 && $matchCount >= 3) {
                $wins[] = [
                    'line' => $positions,
                    'matches' => $matches,
                    'count' => $matchCount,
                    'moneyWon' => $this->lookupPayout($matches, $matchCount),
                ];
            }
        }

        $this->paylines_result = $wins;
    }

    /**
     * Handle special symbols (this modifies the screen in place)
     * @return array
     */
    private function handleSpecialSymbols(): array
    {
        $details = [];
        // Special symbol conversion, assuming ID 10 is the special symbol
        $conversionTarget = null;
        foreach ($this->screen as $reel) {
            foreach ($reel as $symbol) {
                if ($symbol->id == 10 && !$conversionTarget) { // Find the first symbol for conversion
                    continue;
                } elseif ($conversionTarget === null) {
                    $conversionTarget = $symbol->id; // Set conversion target to the first normal symbol found
                    break;
                }
            }
        }

        foreach ($this->screen as $reelIndex => $reel) {
            foreach ($reel as $symbol) {
                if ($symbol->id == 10) { // Convert special symbols
                    $symbol->id = $conversionTarget;
                    $details[] = "Special symbol(10) converted to $conversionTarget";
                }
            }
        }

        return $details;
    }

    /**
     * Lookup the payout for a sequence of symbols
     * @param $matches
     * @param $matchCount
     * @return float|int
     */
    private function lookupPayout($matches, $matchCount): float|int
    {
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
