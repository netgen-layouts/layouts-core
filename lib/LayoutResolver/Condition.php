<?php

namespace Netgen\BlockManager\LayoutResolver;

class Condition
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $valueIdentifier;

    /**
     * @var array
     */
    public $values;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $valueIdentifier
     * @param array $values
     */
    public function __construct($identifier, $valueIdentifier, array $values)
    {
        $this->identifier = $identifier;
        $this->valueIdentifier = $valueIdentifier;
        $this->values = $values;
    }
}
