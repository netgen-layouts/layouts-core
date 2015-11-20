<?php

namespace Netgen\BlockManager\LayoutResolver;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    protected $rules = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule[] $rules
     */
    public function __construct(array $rules = array())
    {
        $this->rules = $rules;
    }

    /**
     * Resolves the layout based on current conditions.
     *
     * @return int|null
     */
    public function resolveLayout()
    {
        foreach ($this->rules as $rule) {
            if (!$rule->target->matches()) {
                continue;
            }

            if (empty($rule->conditions) || $this->matchConditions($rule->conditions)) {
                return $rule->layoutId;
            }
        }

        return null;
    }

    /**
     * Returns true if all conditions match.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\ConditionInterface[] $conditions
     *
     * @return bool
     */
    protected function matchConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (!$condition->matches()) {
                return false;
            }
        }

        return true;
    }
}
