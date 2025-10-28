<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Context\Stubs;

use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextProviderInterface;

final class ContextProvider implements ContextProviderInterface
{
    /**
     * @param array<string, mixed> $variables
     */
    public function __construct(
        private array $variables,
    ) {}

    public function provideContext(Context $context): void
    {
        $context->add($this->variables);
    }
}
