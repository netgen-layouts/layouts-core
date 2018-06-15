<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\ViewInterface;

interface RuleTargetViewInterface extends ViewInterface
{
    /**
     * Returns the target.
     */
    public function getTarget(): Target;
}
