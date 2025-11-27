<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Plugin;

final class SimplePlugin implements PluginInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private(set) string $templateName,
        private(set) array $parameters = [],
    ) {}
}
