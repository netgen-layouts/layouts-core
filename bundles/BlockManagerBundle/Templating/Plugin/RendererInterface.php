<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Plugin;

interface RendererInterface
{
    /**
     * Renders all the registered plugins with provided name.
     *
     * Provided parameters are injected into every plugin template.
     *
     * Parameters provided by plugins themselves override any parameters
     * specified here.
     */
    public function renderPlugins(string $pluginName, array $parameters = []): string;
}
