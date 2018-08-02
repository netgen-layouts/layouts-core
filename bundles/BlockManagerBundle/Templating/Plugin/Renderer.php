<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Plugin;

use Throwable;
use Twig\Environment;

final class Renderer implements RendererInterface
{
    /**
     * @var \Twig\Environment;
     */
    private $twig;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\PluginCollection[]
     */
    private $pluginCollections = [];

    public function __construct(
        Environment $twig,
        array $pluginsByPluginName
    ) {
        $this->twig = $twig;

        foreach ($pluginsByPluginName as $pluginName => $plugins) {
            $this->pluginCollections[$pluginName] = new PluginCollection(
                $pluginName,
                $plugins
            );
        }
    }

    public function renderPlugins(string $pluginName, array $parameters = []): string
    {
        if (!isset($this->pluginCollections[$pluginName])) {
            return '';
        }

        $level = ob_get_level();
        ob_start();

        try {
            foreach ($this->pluginCollections[$pluginName]->getPlugins() as $plugin) {
                $this->twig->display(
                    $plugin->getTemplateName(),
                    $plugin->getParameters() + $parameters
                );
            }
        } catch (Throwable $t) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $t;
        }

        return (string) ob_get_clean();
    }
}
