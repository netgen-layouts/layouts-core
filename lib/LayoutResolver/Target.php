<?php

namespace Netgen\BlockManager\LayoutResolver;

class Target
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var array
     */
    public $values;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $values
     */
    public function __construct($identifier, array $values)
    {
        $this->identifier = $identifier;
        $this->values = $values;
    }
}
