<?php

namespace Netgen\BlockManager\View;

interface RuleConditionViewInterface extends ViewInterface
{
    /**
     * Returns the condition.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function getCondition();
}
