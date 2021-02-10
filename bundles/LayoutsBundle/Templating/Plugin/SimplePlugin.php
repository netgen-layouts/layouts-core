<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Plugin;

final class SimplePlugin implements PluginInterface
{
    private string $templateName;

    /**
     * @var array<string, mixed>
     */
    private array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(string $templateName, array $parameters = [])
    {
        $this->templateName = $templateName;
        $this->parameters = $parameters;
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
