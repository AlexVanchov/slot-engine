<?php

namespace Core\Models;

/**
 * Class Symbol
 * @package Core\Models
 */
class Symbol
{
    public int $id;
    public string $type;

    public function __construct($id, $type)
    {
        $this->id = $id;
        $this->type = $type;
    }
}
