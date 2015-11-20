<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

use InvalidArgumentException;

abstract class Condition implements ConditionInterface
{
    /**
     * @var mixed
     */
    protected $what;

    /**
     * @var array
     */
    protected $values;

    /**
     * Sets the "what" part of this condition.
     *
     * @param mixed $what
     */
    public function setWhat($what)
    {
        $this->what = $what;
    }

    /**
     * Returns the "what" part of this condition.
     *
     * @return mixed
     */
    public function getWhat()
    {
        return $this->what;
    }

    /**
     * Sets the values of this condition.
     *
     * @throws \InvalidArgumentException If condition values are empty
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('Condition values must be a non empty array.');
        }

        $this->values = $values;
    }

    /**
     * Returns the values from the condition.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
