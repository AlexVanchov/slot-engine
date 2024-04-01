<?php

namespace Core\Models;

/**
 * Class Payline
 * @package Core\Models
 */
class Payline
{
    private array $positions;

    /**
     * @param array $positions
     */
    public function __construct(array $positions = [])
    {
        $this->positions = $positions;
    }

    /**
     * Get the positions of the payline
     * @return array
     */
    public function getPositions(): array
    {
        return $this->positions;
    }
}
