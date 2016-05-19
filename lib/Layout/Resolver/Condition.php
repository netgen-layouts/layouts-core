<?php

namespace Netgen\BlockManager\Layout\Resolver;

class Condition
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $parameters;

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

    /**
     * Returns the identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
