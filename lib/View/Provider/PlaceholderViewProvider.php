<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\View\View\PlaceholderView;
use Netgen\Layouts\View\ViewInterface;

final class PlaceholderViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        if (!isset($parameters['block'])) {
            throw ViewProviderException::noParameter('placeholder', 'block');
        }

        if (!$parameters['block'] instanceof Block) {
            throw ViewProviderException::invalidParameter('placeholder', 'block', Block::class);
        }

        return new PlaceholderView($value, $parameters['block']);
    }

    public function supports($value): bool
    {
        return $value instanceof Placeholder;
    }
}
