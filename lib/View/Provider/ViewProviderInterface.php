<?php

namespace Netgen\BlockManager\View\Provider;

/**
 * For every view, an instance of this interface needs to be implemented with
 * creates the view based on provided value object and parameters.
 *
 * Basically, implementations of this interface are view factories.
 */
interface ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array());

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $valueObject
     *
     * @return bool
     */
    public function supports($valueObject);
}
