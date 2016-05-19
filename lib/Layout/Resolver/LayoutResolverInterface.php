<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface LayoutResolverInterface
{
    /**
     * Resolves the layout based on current conditions or null if no rule is available.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule|null
     */
    public function resolveLayout();

    /**
     * Resolves the layout based on provided target or null if no rule is available.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule|null
     */
    public function resolveLayoutForTarget(Target $target);
}
