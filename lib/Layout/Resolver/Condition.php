<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\ValueObject;

class Condition extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $parameters = array();

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
