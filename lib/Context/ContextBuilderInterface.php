<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

interface ContextBuilderInterface
{
    /**
     * Builds the provided context by using all registered context providers.
     */
    public function buildContext(ContextInterface $context): void;
}
