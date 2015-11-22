<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder;

interface TargetBuilderInterface
{
    /**
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier();

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Target
     */
    public function buildTarget();
}
