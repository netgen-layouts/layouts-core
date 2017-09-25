<?php

namespace Netgen\BlockManager\View\Matcher\RuleCondition;

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

        return in_array($view->getCondition()->getConditionType()->getType(), $config, true);
    }
}
