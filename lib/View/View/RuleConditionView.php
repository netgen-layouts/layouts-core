<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class RuleConditionView extends View implements RuleConditionViewInterface
{
    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function getCondition()
    {
        return $this->parameters['condition'];
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'rule_condition_view';
    }
}
