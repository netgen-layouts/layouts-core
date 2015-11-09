<?php

namespace Netgen\BlockManager\Registry\ViewBuilderRegistry;

use Netgen\BlockManager\Registry\ViewBuilderRegistry;
use Netgen\BlockManager\View\Builder\ViewBuilder;
use InvalidArgumentException;

class ArrayBased implements ViewBuilderRegistry
{
    /**
     * @var \Netgen\BlockManager\View\Builder\ViewBuilder[]
     */
    protected $viewBuilders = array();

    /**
     * Adds a view builder to registry.
     *
     * @param \Netgen\BlockManager\View\Builder\ViewBuilder $viewBuilder
     * @param string $type
     */
    public function addViewBuilder(ViewBuilder $viewBuilder, $type)
    {
        $this->viewBuilders[$type] = $viewBuilder;
    }

    /**
     * Returns a view builder for specified object.
     *
     * @param mixed $object
     *
     * @return \Netgen\BlockManager\View\Builder\ViewBuilder
     */
    public function getViewBuilder($object)
    {
        $type = get_class($object);
        if (isset($this->viewBuilders[$type])) {
            return $this->viewBuilders[$type];
        }

        throw new InvalidArgumentException('View builder for "' . $type . '" object does not exist.');
    }

    /**
     * Returns all view builders from registry.
     *
     * @return \Netgen\BlockManager\View\Builder\ViewBuilder[]
     */
    public function getViewBuilders()
    {
        return $this->viewBuilders;
    }
}
