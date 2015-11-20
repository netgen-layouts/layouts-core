<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

abstract class Target implements TargetInterface
{
    /**
     * @var array
     */
    protected $values;

    /**
     * Sets the values of this target.
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
