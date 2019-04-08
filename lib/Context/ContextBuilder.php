<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

final class ContextBuilder implements ContextBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Context\ContextProviderInterface[]
     */
    private $contextProviders = [];

    public function __construct(iterable $contextProviders)
    {
        foreach ($contextProviders as $key => $contextProvider) {
            if ($contextProvider instanceof ContextProviderInterface) {
                $this->contextProviders[$key] = $contextProvider;
            }
        }
    }

    public function buildContext(ContextInterface $context): void
    {
        foreach ($this->contextProviders as $contextProvider) {
            $contextProvider->provideContext($context);
        }
    }
}
