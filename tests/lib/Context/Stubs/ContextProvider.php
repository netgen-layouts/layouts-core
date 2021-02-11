<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Context\Stubs;

use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextProviderInterface;

final class ContextProvider implements ContextProviderInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $variables;

    /**
     * @param array<string, mixed> $variables
     */
    public function __construct(array $variables)
    {
        $this->variables = $variables;
    }

    public function provideContext(Context $context): void
    {
        $context->add($this->variables);
    }
}
