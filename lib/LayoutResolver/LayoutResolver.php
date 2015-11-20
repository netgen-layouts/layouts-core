<?php

namespace Netgen\BlockManager\LayoutResolver;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule\Rule[]
     */
    protected $rules = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\Rule[] $rules
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
        $matchedLayout = null;
        foreach ($this->rules as $rule) {
            if ($rule->matches()) {
                return $rule->getLayoutId();
            }
        }

        return null;
    }
}
