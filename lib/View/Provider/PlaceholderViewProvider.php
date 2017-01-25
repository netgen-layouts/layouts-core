<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Placeholder;
use Netgen\BlockManager\Exception\RuntimeException;
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
            throw new RuntimeException('To build the placeholder view, you need to provide the "block" parameter.');
        }

        if (!$parameters['block'] instanceof Block) {
            throw new RuntimeException(
                sprintf(
                    'To build the placeholder view, "block" parameter needs to be an instance of %s class.',
                    Block::class
                )
            );
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
