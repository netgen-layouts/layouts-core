<?php

namespace Netgen\BlockManager\View\Provider;

interface ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($value);

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return bool
     */
    public function supports($value);
}
