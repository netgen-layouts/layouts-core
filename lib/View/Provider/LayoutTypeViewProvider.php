<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\View\View\LayoutTypeView;
use Netgen\Layouts\View\ViewInterface;

final class LayoutTypeViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        return new LayoutTypeView($value);
    }

    public function supports($value): bool
    {
        return $value instanceof LayoutTypeInterface;
    }
}
