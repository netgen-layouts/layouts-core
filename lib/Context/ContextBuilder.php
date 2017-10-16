<?php

namespace Netgen\BlockManager\Context;

final class ContextBuilder implements ContextBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Context\ContextProviderInterface[]
     */
    private $providers = array();

    public function registerProvider(ContextProviderInterface $contextProvider)
    {
        $this->providers[] = $contextProvider;
    }

    public function buildContext(ContextInterface $context)
    {
        foreach ($this->providers as $provider) {
            $provider->provideContext($context);
        }
    }
}
