<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Plugin;

final class SimplePlugin implements PluginInterface
{
    /**
     * @var string
     */
    private $templateName;

    /**
     * @var array
     */
    private $parameters;

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
