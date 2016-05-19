<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface LayoutResolverInterface
{
    /**
     * Resolves the layout based on current conditions.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule
     */
    public function resolveLayout();

    /**
     * Resolves the layout based on provided target.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule
     */
    public function resolveLayoutForTarget(Target $target);
}
