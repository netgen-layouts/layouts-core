<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\RuleTarget;

use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\RuleTargetViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the target in the provided view
 * has a target type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof RuleTargetViewInterface) {
            return false;
        }

        $targetType = $view->getTarget()->getTargetType();
        if ($targetType instanceof NullTargetType) {
            return in_array('null', $config, true);
        }

        return in_array($targetType::getType(), $config, true);
    }
}
