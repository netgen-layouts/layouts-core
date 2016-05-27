<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface TargetValueProviderInterface
{
    /**
     * Provides the value for the target to be used in matching process.
     *
     * @return mixed
     */
    public function provideValue();
}
