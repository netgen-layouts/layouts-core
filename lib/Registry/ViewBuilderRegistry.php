<?php

namespace Netgen\BlockManager\Registry;

use Netgen\BlockManager\View\Builder\ViewBuilder;

interface ViewBuilderRegistry
{
    /**
     * Adds a view builder to registry.
     *
     * @param \Netgen\BlockManager\View\Builder\ViewBuilder $viewBuilder
     * @param string $type
     */
    public function addViewBuilder(ViewBuilder $viewBuilder, $type);

    /**
     * Returns a view builder for specified object.
     *
     * @param mixed $object
     *
     * @return \Netgen\BlockManager\View\Builder\ViewBuilder
     */
    public function getViewBuilder($object);

    /**
     * Returns all view builders from registry.
     *
     * @return \Netgen\BlockManager\View\Builder\ViewBuilder[]
     */
    public function getViewBuilders();
}
