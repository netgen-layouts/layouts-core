<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\View\View\PlaceholderView;

class PlaceholderViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        if (!isset($parameters['block'])) {
            throw ViewProviderException::noParameter('placeholder', 'block');
        }

        if (!$parameters['block'] instanceof Block) {
            throw ViewProviderException::invalidParameter('placeholder', 'block', Block::class);
        }

        return new PlaceholderView(
            array(
                'placeholder' => $valueObject,
                'block' => $parameters['block'],
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof Placeholder;
    }
}
