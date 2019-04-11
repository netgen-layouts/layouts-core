<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\Bundle\LayoutsBundle\Templating\Plugin\RendererInterface;
use Throwable;

final class PluginRenderingRuntime
{
    /**
     * @var \Netgen\Bundle\LayoutsBundle\Templating\Plugin\RendererInterface
     */
    private $pluginRenderer;

    /**
     * @var \Netgen\BlockManager\Error\ErrorHandlerInterface
     */
    private $errorHandler;

    public function __construct(RendererInterface $pluginRenderer, ErrorHandlerInterface $errorHandler)
    {
        $this->pluginRenderer = $pluginRenderer;
        $this->errorHandler = $errorHandler;
    }

    /**
     * Renders all the template plugins with provided name.
     */
    public function renderPlugins(array $context, string $pluginName): string
    {
        try {
            return $this->pluginRenderer->renderPlugins(
                $pluginName,
                $context
            );
        } catch (Throwable $t) {
            $this->errorHandler->handleError($t);
        }

        return '';
    }
}
