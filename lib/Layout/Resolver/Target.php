<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\ValueObject;

class Target extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $values = array();

    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the values from the target.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
