<?php

namespace Netgen\BlockManager\View\Provider;

/**
 * For every view, an instance of this interface needs to be implemented with
 * creates the view based on provided value and parameters.
 *
 * Basically, implementations of this interface are view factories.
 */
interface ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $value
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($value, array $parameters = []);

    /**
     * Returns if this view provider supports the given value.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return bool
     */
    public function supports($value);
}
