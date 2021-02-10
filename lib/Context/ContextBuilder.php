<?php

declare(strict_types=1);

namespace Netgen\Layouts\Context;

final class ContextBuilder implements ContextBuilderInterface
{
    /**
     * @var \Netgen\Layouts\Context\ContextProviderInterface[]
     */
    private array $contextProviders = [];

    /**
     * @param iterable<\Netgen\Layouts\Context\ContextProviderInterface> $contextProviders
     */
    public function __construct(iterable $contextProviders)
    {
        foreach ($contextProviders as $contextProvider) {
            if ($contextProvider instanceof ContextProviderInterface) {
                $this->contextProviders[] = $contextProvider;
            }
        }
    }

    public function buildContext(Context $context): void
    {
        foreach ($this->contextProviders as $contextProvider) {
            $contextProvider->provideContext($context);
        }
    }
}
