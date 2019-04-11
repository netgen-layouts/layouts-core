<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Block\Block as APIBlock;
use Netgen\Layouts\View\View\BlockView;
use Netgen\Layouts\View\ViewInterface;

final class BlockViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        return new BlockView($value);
    }

    public function supports($value): bool
    {
        return $value instanceof APIBlock;
    }
}
