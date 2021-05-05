<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\Config\ConfigDefinitionFactory;

final class ItemDefinitionFactory
{
    private ConfigDefinitionFactory $configDefinitionFactory;

    public function __construct(ConfigDefinitionFactory $configDefinitionFactory)
    {
        $this->configDefinitionFactory = $configDefinitionFactory;
    }

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
