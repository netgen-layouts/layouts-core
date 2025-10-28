<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

interface RendererInterface
{
    /**
     * Renders the value in the provided view context.
     *
     * @param array<string, mixed> $parameters
     */
    public function renderValue(mixed $value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): string;
}
