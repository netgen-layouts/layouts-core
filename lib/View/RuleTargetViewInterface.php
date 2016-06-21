<?php

namespace Netgen\BlockManager\View;

interface RuleTargetViewInterface extends ViewInterface
{
    /**
     * Returns the target.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function getTarget();
}
