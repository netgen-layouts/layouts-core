<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\View\ViewInterface;

interface RuleConditionViewInterface extends ViewInterface
{
    /**
     * Returns the rule condition.
     */
    public function getCondition(): RuleCondition;
}
