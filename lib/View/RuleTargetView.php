<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;

class RuleTargetView extends View implements RuleTargetViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     */
    public function __construct(Target $target)
    {
        $this->valueObject = $target;
        $this->internalParameters['target'] = $target;
    }

    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function getTarget()
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
        return 'rule_target_view';
    }
}
