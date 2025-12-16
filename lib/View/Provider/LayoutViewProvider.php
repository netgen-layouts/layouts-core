<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\View\View\LayoutView;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\Layout\Layout>
 */
final class LayoutViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): LayoutView
    {
        return new LayoutView($value);
    }

    public function supports(object $value): bool
    {
        return $value instanceof Layout;
    }
}
