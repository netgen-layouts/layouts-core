<?php

namespace Netgen\BlockManager\Layout\Resolver;

class Target
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $values;

    /**
     * Constructor.
     *
     * @param $identifier
     * @param array $values
     */
    public function __construct($identifier, array $values = array())
    {
        $this->identifier = $identifier;
        $this->values = $values;
    }

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
