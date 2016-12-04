<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class RuleView extends View implements RuleViewInterface
{
    /**
     * Returns the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function getRule()
    {
        return $this->parameters['rule'];
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'rule_view';
    }
}
