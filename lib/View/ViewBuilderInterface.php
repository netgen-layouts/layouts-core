<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

interface ViewBuilderInterface
{
    /**
     * Builds the view from the provided value in specified context.
     *
     * @param mixed $value
     * @param array<string, mixed> $parameters
     */
    public function buildView($value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): ViewInterface;
}
