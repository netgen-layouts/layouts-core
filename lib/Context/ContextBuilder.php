<?php

declare(strict_types=1);

namespace Netgen\Layouts\Context;

final class ContextBuilder implements ContextBuilderInterface
{
    /**
     * @param iterable<\Netgen\Layouts\Context\ContextProviderInterface> $contextProviders
     */
    public function __construct(
        private iterable $contextProviders,
    ) {}

    public function buildContext(Context $context): void
    {
        foreach ($this->contextProviders as $contextProvider) {
            $contextProvider->provideContext($context);
        }
    }
}
