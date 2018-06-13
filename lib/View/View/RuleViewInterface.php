<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface RuleViewInterface extends ViewInterface
{
    /**
     * Returns the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function getRule();
}
