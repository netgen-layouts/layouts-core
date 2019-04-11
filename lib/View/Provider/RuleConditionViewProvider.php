<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\View\View\RuleConditionView;
use Netgen\Layouts\View\ViewInterface;

final class RuleConditionViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        return new RuleConditionView($value);
    }

    public function supports($value): bool
    {
        return $value instanceof Condition;
    }
}
