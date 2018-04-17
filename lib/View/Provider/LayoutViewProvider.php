<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\View\View\LayoutView;

final class LayoutViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = [])
    {
        return new LayoutView(
            [
                'layout' => $value,
            ]
        );
    }

    public function supports($value)
    {
        return $value instanceof Layout;
    }
}
