<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder;

interface TargetBuilderInterface
{
    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\TargetInterface
     */
    public function buildTarget();
}
