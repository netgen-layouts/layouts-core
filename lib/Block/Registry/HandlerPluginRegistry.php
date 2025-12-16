<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use function is_a;

final class HandlerPluginRegistry
{
    /**
     * @param iterable<\Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface> $handlerPlugins
     */
    public function __construct(
        private iterable $handlerPlugins,
    ) {}

    /**
     * Returns all handler plugins for the provided handler class.
     *
     * @return iterable<\Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface>
     */
    public function getPlugins(string $identifier, string $handlerClass): iterable
    {
        foreach ($this->handlerPlugins as $plugin) {
            foreach ($plugin::getExtendedIdentifiers() as $extendedIdentifier) {
                if ($extendedIdentifier === $identifier) {
                    yield $plugin;

                    continue 2;
                }
            }

            foreach ($plugin::getExtendedHandlers() as $extendedHandler) {
                if (is_a($handlerClass, $extendedHandler, true)) {
                    yield $plugin;

                    continue 2;
                }
            }
        }
    }
}
