<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface;

final class HandlerPluginRegistry implements HandlerPluginRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[]
     */
    private $handlerPlugins;

    /**
     * @param \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[] $handlerPlugins
     */
    public function __construct(array $handlerPlugins)
    {
        $this->handlerPlugins = array_filter(
            $handlerPlugins,
            static function (PluginInterface $handlerPlugin): bool {
                return true;
            }
        );
    }

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
