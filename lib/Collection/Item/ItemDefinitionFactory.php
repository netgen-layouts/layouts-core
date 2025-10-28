<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\Config\ConfigDefinitionFactory;

final class ItemDefinitionFactory
{
    public function __construct(
        private ConfigDefinitionFactory $configDefinitionFactory,
    ) {}

    /**
     * Builds the item definition.
     *
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     */
    public function buildItemDefinition(string $valueType, array $configDefinitionHandlers): ItemDefinitionInterface
    {
        $configDefinitions = [];
        foreach ($configDefinitionHandlers as $configKey => $configDefinitionHandler) {
            $configDefinitions[$configKey] = $this->configDefinitionFactory->buildConfigDefinition(
                $configKey,
                $configDefinitionHandler,
            );
        }

        return ItemDefinition::fromArray(
            [
                'valueType' => $valueType,
                'configDefinitions' => $configDefinitions,
            ],
        );
    }
}
