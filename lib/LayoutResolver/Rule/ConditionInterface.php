<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

interface ConditionInterface
{
    /**
     * Sets the identifier of this condition.
     *
     * @param int|string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * Returns the identifier of this condition.
     *
     * @return int|string
     */
    public function getIdentifier();

    /**
     * Sets the values of this condition.
     *
     * @throws \InvalidArgumentException If condition values are empty
     *
     * @param array $values
     */
    public function setValues(array $values);

    /**
     * Returns the values from the condition.
     *
     * @return array
     */
    public function getValues();

    /**
     * Returns if this condition matches.
     *
     * @return bool
     */
    public function matches();

    /**
     * Returns if this condition supports the given target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface
     *
     * @return bool
     */
    public function supports(TargetInterface $target);
}
