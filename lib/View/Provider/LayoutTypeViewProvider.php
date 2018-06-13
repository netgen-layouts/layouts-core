<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;
use Netgen\BlockManager\View\View\LayoutTypeView;

final class LayoutTypeViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = [])
    {
        return new LayoutTypeView(
            [
                'layoutType' => $value,
            ]
        );
    }

    public function supports($value)
    {
        return $value instanceof LayoutTypeInterface;
    }
}
