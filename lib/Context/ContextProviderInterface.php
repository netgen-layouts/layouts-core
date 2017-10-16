<?php

namespace Netgen\BlockManager\Context;

interface ContextProviderInterface
{
    /**
     * Updates the provided context with a set of scalar variables.
     *
     * @param \Netgen\BlockManager\Context\ContextInterface $context
     */
    public function provideContext(ContextInterface $context);
}
