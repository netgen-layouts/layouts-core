<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\RuleView;
use Netgen\BlockManager\View\ViewInterface;

final class RuleViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        return new RuleView(
            [
                'rule' => $value,
            ]
        );
    }

    public function supports($value): bool
    {
        return $value instanceof Rule;
    }
}
