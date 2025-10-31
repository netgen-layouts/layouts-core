<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Plugin;

use Throwable;
use Twig\Environment;

use function ob_end_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;

final class Renderer implements RendererInterface
{
    /**
     * @var array<string, \Netgen\Bundle\LayoutsBundle\Templating\Plugin\PluginCollection>
     */
    private array $pluginCollections = [];

    /**
     * @param array<string, \Netgen\Bundle\LayoutsBundle\Templating\Plugin\PluginInterface[]> $pluginsByPluginName
     */
    public function __construct(
        private Environment $twig,
        array $pluginsByPluginName,
    ) {
        foreach ($pluginsByPluginName as $pluginName => $plugins) {
            $this->pluginCollections[$pluginName] = new PluginCollection(
                $pluginName,
                $plugins,
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
                    [...$parameters, ...$plugin->getParameters()],
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
