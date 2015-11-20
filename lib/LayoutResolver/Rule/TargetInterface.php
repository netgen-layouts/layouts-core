<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

interface TargetInterface
{
    /**
     * Sets the values of this target.
     *
     * @param array $values
     */
    public function setValues(array $values);

    /**
     * Returns the values from the target.
     *
     * @return array
     */
    public function getValues();

    /**
     * Sets the conditions to this target.
     *
     * @param array $conditions
     */
    public function setConditions(array $conditions = array());

    /**
     * Returns the conditions from the target.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule\ConditionInterface[]
     */
    public function getConditions();

    /**
     * Returns if this target matches, optionally limited
     * to provided conditions.
     *
     * @return bool
     */
    public function matches();
}
