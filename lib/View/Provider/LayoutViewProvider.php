<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewInterface;

final class LayoutViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        return new LayoutView(
            [
                'layout' => $value,
            ]
        );
    }

    public function supports($value): bool
    {
        return $value instanceof Layout;
    }
}
