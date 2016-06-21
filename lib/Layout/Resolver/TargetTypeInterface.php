<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface TargetTypeInterface
{
    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @return mixed
     */
    public function provideValue();
}
