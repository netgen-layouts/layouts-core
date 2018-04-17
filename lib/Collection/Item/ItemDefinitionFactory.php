<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\Config\ConfigDefinitionFactory;

final class ItemDefinitionFactory
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionFactory
     */
    private $configDefinitionFactory;

    public function __construct(ConfigDefinitionFactory $configDefinitionFactory)
    {
        $this->configDefinitionFactory = $configDefinitionFactory;
    }

    /**
     * Builds the item definition.
     *
     * @param string $valueType
     * @param \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     *
     * @return \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    public function buildItemDefinition($valueType, array $configDefinitionHandlers)
    {
        $configDefinitions = [];
        foreach ($configDefinitionHandlers as $configKey => $configDefinitionHandler) {
            $configDefinitions[$configKey] = $this->configDefinitionFactory->buildConfigDefinition(
                $configKey,
                $configDefinitionHandler
            );
        }

        return new ItemDefinition(
            [
                'valueType' => $valueType,
                'configDefinitions' => $configDefinitions,
            ]
        );
    }
}
