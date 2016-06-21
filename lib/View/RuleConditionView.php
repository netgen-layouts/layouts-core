<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;

class RuleConditionView extends View implements RuleConditionViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     */
    public function __construct(Condition $condition)
    {
        $this->valueObject = $condition;
        $this->internalParameters['condition'] = $condition;
    }

    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function getCondition()
    {
        return $this->valueObject;
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'rule_condition_view';
    }
}
