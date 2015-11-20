<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

abstract class Target implements TargetInterface
{
    /**
     * @var array
     */
    protected $values;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule\ConditionInterface[]
     */
    protected $conditions;

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

    /**
     * Sets the conditions to this target.
     *
     * @param array $conditions
     */
    public function setConditions(array $conditions = array())
    {
        $this->conditions = $conditions;
    }

    /**
     * Returns the conditions from the target.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule\ConditionInterface[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Returns if this target matches, optionally limited
     * to provided conditions.
     *
     * @return bool
     */
    public final function matches()
    {
        if (!$this->evaluate()) {
            return false;
        }

        if (empty($this->conditions)) {
            return true;
        }

        $matched = false;

        foreach ($this->conditions as $condition) {
            if (!$condition->supports($this)) {
                continue;
            }

            if (!$condition->matches()) {
                return false;
            }

            // We need the flag to know if at least one condition
            // that supports the target matched
            $matched = true;
        }

        return $matched;
    }

    /**
     * Evaluates if values of this target match.
     *
     * @return bool
     */
    abstract protected function evaluate();
}
