<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

interface RendererInterface
{
    /**
     * Renders the value in the provided view context.
     *
     * @param mixed $value
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderValue($value, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []);
}
