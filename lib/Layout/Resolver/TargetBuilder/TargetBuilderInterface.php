<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetBuilder;

interface TargetBuilderInterface
{
    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetInterface
     */
    public function buildTarget();
}
