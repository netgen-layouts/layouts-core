<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface;

use function array_filter;
use function array_values;
use function is_a;
use function method_exists;

final class HandlerPluginRegistry
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface[]
     */
    private array $handlerPlugins = [];

    /**
     * @param iterable<\Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface> $handlerPlugins
     */
    public function __construct(iterable $handlerPlugins)
    {
        foreach ($handlerPlugins as $handlerPlugin) {
            if ($handlerPlugin instanceof PluginInterface) {
                $this->handlerPlugins[] = $handlerPlugin;
            }
        }
    }

    /**
     * Returns all handler plugins for the provided handler class.
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface[]
     */
    public function getPlugins(string $identifier, string $handlerClass): array
    {
        return array_values(
            array_filter(
                $this->handlerPlugins,
                static function (PluginInterface $plugin) use ($identifier, $handlerClass): bool {
                    if (method_exists($plugin, 'getExtendedIdentifiers')) {
                        foreach ($plugin::getExtendedIdentifiers() as $extendedIdentifier) {
                            if ($extendedIdentifier === $identifier) {
                                return true;
                            }
                        }
                    }

                    foreach ($plugin::getExtendedHandlers() as $extendedHandler) {
                        if (is_a($handlerClass, $extendedHandler, true)) {
                            return true;
                        }
                    }

                    return false;
                },
            ),
        );
    }
}
