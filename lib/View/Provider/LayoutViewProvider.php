<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\View\View\LayoutView;
use Netgen\Layouts\View\ViewInterface;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\Layout\Layout>
 */
final class LayoutViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        return new LayoutView($value);
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof Layout;
    }
}
