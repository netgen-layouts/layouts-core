<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\RuleCondition;

use Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\RuleConditionViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the condition in the provided view
 * has a condition type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof RuleConditionViewInterface) {
            return false;
        }

        $conditionType = $view->getCondition()->getConditionType();
        if ($conditionType instanceof NullConditionType) {
            return in_array('null', $config, true);
        }

        return in_array($conditionType->getType(), $config, true);
    }
}
