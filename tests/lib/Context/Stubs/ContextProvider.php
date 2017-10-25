<?php

namespace Netgen\BlockManager\Tests\Context\Stubs;

use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Context\ContextProviderInterface;

class ContextProvider implements ContextProviderInterface
{
    /**
     * @var array
     */
    private $variables;

    public function __construct(array $variables = array())
    {
        $this->variables = $variables;
    }

    public function provideContext(ContextInterface $context)
    {
        $context->add($this->variables);
    }
}
