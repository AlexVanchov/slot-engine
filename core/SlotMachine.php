<?php

namespace Core;

use Core\Models\Payline;
use Core\Models\Reel;
use Core\Models\Symbol;
use Core\Services\ConfigLoader;

/**
 * This class represents the slot machine game. It handles spinning the reels and evaluating the paylines.
 * It uses the ConfigLoader to load the configuration from a JSON file.
 * It uses the Symbol, Reel, and Payline classes to represent the game entities.
 *
 * Class SlotMachine
 * @package Core
 */
class SlotMachine
{
    private ConfigLoader $configLoader;
    private array $config_reels = [];
    private array $config_lines = [];
    private array $config_tiles = [];
    private array $config_pays = [];
    private float $stake;

    /**
     * @var array Use this to store details on any symbols that have been converted
     */
    private array $details = [];
    private array $screen = [];
    private array $paylines = [];

    /**
     * @throws \Exception
     */
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
            $this->config_tiles[$tileConfig['id']] = new Symbol(
                $tileConfig['id'],
                $tileConfig['type']
            );
        }

        // Load reels
        foreach ($this->configLoader->getReels()[0] as $reelConfig) {
            $reel = new Reel();
            foreach ($reelConfig as $symbolId) {
                $reel->addSymbol($this->config_tiles[$symbolId]);
            }
            $this->config_reels[] = $reel;
        }

        // Load paylines
        foreach ($this->configLoader->getLines() as $lineConfig) {
            $this->config_lines[] = new Payline($lineConfig);
        }

        // Load pays
        $this->config_pays = $this->configLoader->getPays();
    }

    /**
     * @param $stake
     * @return array
     * @throws \Exception
     */
    public function spin($stake): array
    {
        $this->stake = floatval($stake);
        // validate the stake
        if ($this->stake < 0.1 || $this->stake > 10) {
            throw new \Exception("Stake must be between 0.1 and 10");
        }

        $this->setRandomScreen();
        $this->evaluatePaylines();

        return [
            'screen' => $this->screen, // The 5x3 grid of symbols
            'details' => $this->details, // Details on any conversions that took place
            'paylines' => $this->paylines, // Information on paylines that have won
        ];
    }

    /**
     * Generate a 5x3 grid of symbols
     * @return void
     */
    private function setRandomScreen(): void
    {
        $this->screen = []; // This will hold the 5x3 grid of symbols

        /**
         * @var Reel $reel
         */
        foreach ($this->config_reels as $reelIndex => $reel) {
            $symbols = $reel->getSymbols();
            $symbolsCount = count($symbols);
            $reelPosition = rand(0, $symbolsCount - 1); // Random starting position for this reel

            for ($col = 0; $col < 3; $col++) {
                $position = ($reelPosition + $col) % $symbolsCount; // Wrap around if necessary
                $this->screen[$reelIndex][$col] = $symbols[$position];
            }
        }
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
        foreach ($this->config_lines as $line) {
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
                    $this->checkForWin($wins, $matches, $matchCount, $positions);
                    // Reset for the next sequence of matches
                    $matches = [$currentSymbol->id];
                    $matchCount = 1;
                }
                $prevSymbolId = $currentSymbol->id;
            }

            // Check for a win after the loop
            $this->checkForWin($wins, $matches, $matchCount, $positions);
        }

        $this->paylines = $wins;
    }

    /**
     * Checks if a sequence of symbols is a win and adds it to the wins array
     * @param array $wins
     * @param array $matches
     * @param int $matchCount
     * @param array $positions
     * @return void
     */
    private function checkForWin(array &$wins, array $matches, int $matchCount, array $positions): void
    {
        if (count(array_unique($matches)) === 1 && $matchCount >= 3) {
            $wins[] = [
                'line' => $positions,
                'matches' => $matches,
                'count' => $matchCount,
                'moneyWon' => $this->lookupPayout($matches, $matchCount),
            ];
        }
    }

    /**
     * Handle special symbols (this modifies the screen in place)
     * @return array
     */
    private function handleSpecialSymbols(): array
    {
        $details = [];
        $conversionTarget = null;

        foreach ($this->screen as $reelIndex => $reel) {
            foreach ($reel as $symbolIndex => $symbol) {
                $isMystery = $symbol->type == Symbol::TYPE_MYSTERY;
                if ($isMystery) {
                    if ($conversionTarget === null) {
                        // Find the next non-special symbol in the screen
                        $conversionTarget = $this->findClosestNonSpecialSymbol($reelIndex, $symbolIndex);
                        if ($conversionTarget === null) {
                            // If no non-special symbol is found, find the previous non-special symbol
                            $conversionTarget = $this->findClosestNonSpecialSymbol($reelIndex, $symbolIndex, 'backward');
                        }
                    }
                    $symbol->id = $conversionTarget;
                    $details[] = "Special symbol(10) converted to $conversionTarget";
                }
            }
        }

        return $details;
    }

    /**
     * Find the neares non-special symbol in the screen
     * @param int $startReelIndex
     * @param int $startSymbolIndex
     * @param string $direction
     * @return int|null
     */
    private function findClosestNonSpecialSymbol(int $startReelIndex, int $startSymbolIndex, string $direction = 'forward'): ?int
    {
        $reels = $direction === 'backward' ? array_reverse($this->screen, true) : $this->screen;
        foreach ($reels as $reelIndex => $reel) {
            if ($direction === 'backward' && $reelIndex > $startReelIndex) {
                continue;
            }
            if ($direction === 'forward' && $reelIndex < $startReelIndex) {
                continue;
            }

            $symbols = $direction === 'backward' ? array_reverse($reel) : $reel;
            foreach ($symbols as $symbolIndex => $symbol) {
                if ($reelIndex == $startReelIndex && $direction === 'backward' && $symbolIndex >= $startSymbolIndex) {
                    continue;
                }
                if ($reelIndex == $startReelIndex && $direction === 'forward' && $symbolIndex <= $startSymbolIndex) {
                    continue;
                }

                if ($symbol->type != Symbol::TYPE_MYSTERY) {
                    return $symbol->id;
                }
            }
        }

        return null; // No non-special symbol found
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
        foreach ($this->config_pays as $pay) {
            if ($pay[0] == $symbolId && $pay[1] == $matchCount) {
                // calculate the payout amount based on the stake
                return $pay[2] * $this->stake;// Return the payout amount
            }
        }

        return 0; // No payout for this sequence
    }

}
