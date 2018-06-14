<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

interface ContextBuilderInterface
{
    /**
     * Register a context provider to the builder.
     */
    public function registerProvider(ContextProviderInterface $contextProvider): void;

    /**
     * Builds the provided context by using all registered context providers.
     */
    public function buildContext(ContextInterface $context): void;
}
