<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;

class RuleView extends View implements RuleViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     */
    public function __construct(Rule $rule)
    {
        $this->valueObject = $rule;

        $this->internalParameters['rule'] = $rule;
    }

    /**
     * Returns the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function getRule()
    {
        return $this->valueObject;
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
