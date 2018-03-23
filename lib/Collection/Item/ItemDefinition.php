<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;
use Netgen\BlockManager\Value;

/**
 * @final
 */
class ItemDefinition extends Value implements ItemDefinitionInterface
{
    use ConfigDefinitionAwareTrait;

    /**
     * @var string
     */
    protected $valueType;

    public function getValueType()
    {
        return $this->valueType;
    }
}
