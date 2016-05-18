<?php

namespace Netgen\BlockManager\Layout\Resolver;

class Condition
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var array
     */
    public $parameters;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $parameters
     */
    public function __construct($identifier, array $parameters)
    {
        $this->identifier = $identifier;
        $this->parameters = $parameters;
    }
}
