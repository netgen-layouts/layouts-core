<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

interface ContextBuilderInterface
{
    /**
     * Register a context provider to the builder.
     *
     * @param \Netgen\BlockManager\Context\ContextProviderInterface $contextProvider
     */
    public function registerProvider(ContextProviderInterface $contextProvider);

    /**
     * Builds the provided context by using all registered context providers.
     *
     * @param \Netgen\BlockManager\Context\ContextInterface $context
     */
    public function buildContext(ContextInterface $context);
}
