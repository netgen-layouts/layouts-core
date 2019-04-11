<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\Config\ConfigDefinitionFactory;

final class ItemDefinitionFactory
{
    /**
     * @var \Netgen\Layouts\Config\ConfigDefinitionFactory
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
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     *
     * @return \Netgen\Layouts\Collection\Item\ItemDefinitionInterface
     */
    public function buildItemDefinition(string $valueType, array $configDefinitionHandlers): ItemDefinitionInterface
    {
        $configDefinitions = [];
        foreach ($configDefinitionHandlers as $configKey => $configDefinitionHandler) {
            $configDefinitions[$configKey] = $this->configDefinitionFactory->buildConfigDefinition(
                $configKey,
                $configDefinitionHandler
            );
        }

        return ItemDefinition::fromArray(
            [
                'valueType' => $valueType,
                'configDefinitions' => $configDefinitions,
            ]
        );
    }
}
