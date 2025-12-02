<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\View\View\RuleConditionView;
use Netgen\Layouts\View\ViewInterface;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\LayoutResolver\Condition>
 */
final class RuleConditionViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        return new RuleConditionView($value);
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof Condition;
    }
}
