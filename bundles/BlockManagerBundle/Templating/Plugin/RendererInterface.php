<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Plugin;

interface RendererInterface
{
    /**
     * Renders all the registered plugins with provided names.
     *
     * Provided parameters are injected into every plugin template.
     */
    public function renderPlugins(string $pluginName, array $parameters = []): string;
}
