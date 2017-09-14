<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class RuleConditionView extends View implements RuleConditionViewInterface
{
    public function getCondition()
    {
        return $this->parameters['condition'];
    }

    public function getIdentifier()
    {
        return 'rule_condition_view';
    }
}
