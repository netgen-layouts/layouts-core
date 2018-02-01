<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Tests\Config\Stubs\CollectionItem\VisibilityConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;

final class ItemDefinition implements ItemDefinitionInterface
{
    /**
     * @var string
     */
    private $valueType;

    /**
     * @param string $valueType
     */
    public function __construct($valueType)
    {
        $this->valueType = $valueType;
    }

    /**
     * Returns the value type for this definition.
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions()
    {
        return array(
            'visibility' => new ConfigDefinition('visibility', new VisibilityConfigHandler()),
        );
    }
}
