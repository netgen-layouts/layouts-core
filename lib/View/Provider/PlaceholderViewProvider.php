<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\View\View\PlaceholderView;

final class PlaceholderViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = [])
    {
        if (!isset($parameters['block'])) {
            throw ViewProviderException::noParameter('placeholder', 'block');
        }

        if (!$parameters['block'] instanceof Block) {
            throw ViewProviderException::invalidParameter('placeholder', 'block', Block::class);
        }

        return new PlaceholderView(
            [
                'placeholder' => $value,
                'block' => $parameters['block'],
            ]
        );
    }

    public function supports($value)
    {
        return $value instanceof Placeholder;
    }
}
