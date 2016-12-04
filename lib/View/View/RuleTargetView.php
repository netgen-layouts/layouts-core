<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class RuleTargetView extends View implements RuleTargetViewInterface
{
    /**
     * Returns the target.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function getTarget()
    {
        return $this->parameters['target'];
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'rule_target_view';
    }
}
