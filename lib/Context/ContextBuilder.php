<?php

declare(strict_types=1);

namespace Netgen\Layouts\Context;

final class ContextBuilder implements ContextBuilderInterface
{
    /**
     * @var \Netgen\Layouts\Context\ContextProviderInterface[]
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

    public function buildContext(Context $context): void
    {
        foreach ($this->contextProviders as $contextProvider) {
            $contextProvider->provideContext($context);
        }
    }
}
