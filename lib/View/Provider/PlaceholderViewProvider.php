<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\View\View\PlaceholderView;

class PlaceholderViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
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

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof Placeholder;
    }
}
