<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Block\Block as APIBlock;
use Netgen\Layouts\View\View\BlockView;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\Block\Block>
 */
final class BlockViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): BlockView
    {
        return new BlockView($value);
    }

    public function supports(object $value): bool
    {
        return $value instanceof APIBlock;
    }
}
