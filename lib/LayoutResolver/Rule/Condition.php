<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

use InvalidArgumentException;

abstract class Condition implements ConditionInterface
{
    /**
     * @var int|string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $values;

    /**
     * Sets the identifier of this condition.
     *
     * @param int|string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Returns the identifier of this condition.
     *
     * @return int|string
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
