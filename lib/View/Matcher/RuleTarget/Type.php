<?php

namespace Netgen\BlockManager\View\Matcher\RuleTarget;

use Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\RuleTargetViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the target in the provided view
 * has a target type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof RuleTargetViewInterface) {
            return false;
        }

        $targetType = $view->getTarget()->getTargetType();
        if ($targetType instanceof NullTargetType) {
            return in_array('null', $config, true);
        }

        return in_array($targetType->getType(), $config, true);
    }
}
