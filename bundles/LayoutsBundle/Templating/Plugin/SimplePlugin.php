<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Plugin;

final class SimplePlugin implements PluginInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        public private(set) string $templateName,
        public private(set) array $parameters = [],
    ) {}
}
