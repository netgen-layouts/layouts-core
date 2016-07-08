<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface RuleConditionViewInterface extends ViewInterface
{
    /**
     * Returns the condition.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function getCondition();
}
