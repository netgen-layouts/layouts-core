<?php

namespace Netgen\BlockManager\LayoutResolver;

interface LayoutResolverInterface
{
    /**
     * Resolves the layout based on current conditions.
     *
     * @return int|null
     */
    public function resolveLayout();
}
