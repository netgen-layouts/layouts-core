<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Context\Stubs;

use Netgen\Layouts\Context\ContextInterface;
use Netgen\Layouts\Context\ContextProviderInterface;

final class ContextProvider implements ContextProviderInterface
{
    /**
     * @var array
     */
    private $variables;

    public function __construct(array $variables)
    {
        $this->variables = $variables;
    }

    public function provideContext(ContextInterface $context): void
    {
        $context->add($this->variables);
    }
}
