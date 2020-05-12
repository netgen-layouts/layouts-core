<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

interface RendererInterface
{
    /**
     * Renders the value in the provided view context.
     *
     * @param mixed $value
     * @param array<string, mixed> $parameters
     */
    public function renderValue($value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): string;
}
