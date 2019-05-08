<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface;

final class HandlerPluginRegistry
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface[]
     */
    private $handlerPlugins = [];

    public function __construct(iterable $handlerPlugins)
    {
        foreach ($handlerPlugins as $key => $handlerPlugin) {
            if ($handlerPlugin instanceof PluginInterface) {
                $this->handlerPlugins[$key] = $handlerPlugin;
            }
        }
    }

    /**
     * Returns all handler plugins for the provided handler class.
     *
     * @param string $handlerClass
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface[]
     */
    public function getPlugins(string $handlerClass): array
    {
        return array_values(
            array_filter(
                $this->handlerPlugins,
                static function (PluginInterface $plugin) use ($handlerClass): bool {
                    $extendedHandlers = $plugin::getExtendedHandlers();
                    foreach ($extendedHandlers as $extendedHandler) {
                        if (is_a($handlerClass, $extendedHandler, true)) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }
}
