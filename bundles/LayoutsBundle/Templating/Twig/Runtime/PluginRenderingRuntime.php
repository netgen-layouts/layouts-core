<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Netgen\Bundle\LayoutsBundle\Templating\Plugin\RendererInterface;
use Netgen\Layouts\Error\ErrorHandlerInterface;
use Throwable;

final class PluginRenderingRuntime
{
    private RendererInterface $pluginRenderer;

    private ErrorHandlerInterface $errorHandler;

    public function __construct(RendererInterface $pluginRenderer, ErrorHandlerInterface $errorHandler)
    {
        $this->pluginRenderer = $pluginRenderer;
        $this->errorHandler = $errorHandler;
    }

    /**
     * Renders all the template plugins with provided name.
     *
     * @param array<string, mixed> $context
     */
    public function renderPlugins(array $context, string $pluginName): string
    {
        try {
            return $this->pluginRenderer->renderPlugins(
                $pluginName,
                $context,
            );
        } catch (Throwable $t) {
            $this->errorHandler->handleError($t);
        }

        return '';
    }
}
