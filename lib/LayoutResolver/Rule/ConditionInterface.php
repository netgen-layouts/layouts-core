<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

interface ConditionInterface
{
    /**
     * Sets the "what" part of this condition.
     *
     * @param mixed $what
     */
    public function setWhat($what);

    /**
     * Returns the "what" part of this condition.
     *
     * @return mixed
     */
    public function getWhat();

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
