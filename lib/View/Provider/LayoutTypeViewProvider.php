<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\View\View\LayoutTypeView;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\Layout\Type\LayoutTypeInterface>
 */
final class LayoutTypeViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): LayoutTypeView
    {
        return new LayoutTypeView($value);
    }

    public function supports(object $value): bool
    {
        return $value instanceof LayoutTypeInterface;
    }
}
