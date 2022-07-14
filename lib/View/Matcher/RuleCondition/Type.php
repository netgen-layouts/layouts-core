<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\RuleCondition;

use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\RuleConditionViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the condition in the provided view
 * has a condition type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof RuleConditionViewInterface) {
            return false;
        }

        $conditionType = $view->getCondition()->getConditionType();
        if ($conditionType instanceof NullConditionType) {
            return in_array('null', $config, true);
        }

        return in_array($conditionType::getType(), $config, true);
    }
}
