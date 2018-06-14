<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

interface ContextProviderInterface
{
    /**
     * Updates the provided context with a set of scalar variables.
     */
    public function provideContext(ContextInterface $context): void;
}
