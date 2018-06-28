<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Plugin;

final class SimplePlugin implements PluginInterface
{
    /**
     * @var string
     */
    private $templateName;

    public function __construct(string $templateName)
    {
        $this->templateName = $templateName;
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function getParameters(): array
    {
        return [];
    }
}
