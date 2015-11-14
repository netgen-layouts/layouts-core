<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Value;

interface ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param array $parameters
     * @param string $context
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView(Value $value, array $parameters = array(), $context = 'view');

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return bool
     */
    public function supports(Value $value);
}
