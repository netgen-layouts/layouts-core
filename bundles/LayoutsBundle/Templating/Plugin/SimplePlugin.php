<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Plugin;

final class SimplePlugin implements PluginInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private string $templateName,
        private array $parameters = [],
    ) {}

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
