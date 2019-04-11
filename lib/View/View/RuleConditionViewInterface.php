<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\View\ViewInterface;

interface RuleConditionViewInterface extends ViewInterface
{
    /**
     * Returns the condition.
     */
    public function getCondition(): Condition;
}
