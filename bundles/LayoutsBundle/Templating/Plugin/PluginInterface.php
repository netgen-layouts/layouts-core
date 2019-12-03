<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Plugin;

interface PluginInterface
{
    /**
     * Returns the template name that will be used to render the plugin.
     */
    public function getTemplateName(): string;

    /**
     * Returns the template parameters that will be injected into the template.
     *
     * They override any parameters provided by the renderer.
     *
     * @return array<string, mixed>
     */
    public function getParameters(): array;
}
