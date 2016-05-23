<?php

namespace Netgen\BlockManager\View\Provider;

interface ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject);

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $valueObject
     *
     * @return bool
     */
    public function supports($valueObject);
}
