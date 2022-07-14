<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\RuleCondition;

use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\RuleConditionViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the condition in the provided view
 * is a rule or a rule group condition, as specified in the config.
 */
final class IsGroupCondition implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof RuleConditionViewInterface) {
            return false;
        }

        return in_array($view->getCondition() instanceof RuleGroupCondition, $config, true);
    }
}
