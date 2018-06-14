<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\View\RuleConditionView;
use Netgen\BlockManager\View\ViewInterface;

final class RuleConditionViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        return new RuleConditionView(
            [
                'condition' => $value,
            ]
        );
    }

    public function supports($value): bool
    {
        return $value instanceof Condition;
    }
}
