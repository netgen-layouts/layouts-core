<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\View\View\RuleConditionView;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\LayoutResolver\Condition>
 */
final class RuleConditionViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): RuleConditionView
    {
        return new RuleConditionView($value);
    }

    public function supports(object $value): bool
    {
        return $value instanceof Condition;
    }
}
