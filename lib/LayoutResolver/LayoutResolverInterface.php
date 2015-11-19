<?php

namespace Netgen\BlockManager\LayoutResolver;

interface LayoutResolverInterface
{
    /**
     * Resolves the layout based on current conditions.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout|null
     */
    public function resolveLayout();
}
