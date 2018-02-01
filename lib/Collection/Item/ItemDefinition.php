<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;
use Netgen\BlockManager\ValueObject;

/**
 * @final
 */
class ItemDefinition extends ValueObject implements ItemDefinitionInterface
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
