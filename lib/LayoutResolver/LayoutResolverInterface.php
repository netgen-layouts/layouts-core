<?php

namespace Netgen\BlockManager\LayoutResolver;

interface LayoutResolverInterface
{
    /**
     * Resolves the layout based on current conditions.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule
     */
    public function resolveLayout();

    /**
     * Resolves the layout based on provided target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule
     */
    public function resolveLayoutForTarget(Target $target);
}
