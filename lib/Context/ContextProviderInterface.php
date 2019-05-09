<?php

declare(strict_types=1);

namespace Netgen\Layouts\Context;

interface ContextProviderInterface
{
    /**
     * Updates the provided context with a set of scalar variables.
     */
    public function provideContext(Context $context): void;
}
