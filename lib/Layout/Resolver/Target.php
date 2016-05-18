<?php

namespace Netgen\BlockManager\Layout\Resolver;

abstract class Target implements TargetInterface
{
    /**
     * @var array
     */
    protected $values;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     * Sets the values to the target.
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
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
