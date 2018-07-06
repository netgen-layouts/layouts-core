<?php

declare(strict_types=1);

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
    private $valueType;

    public function getValueType(): string
    {
        return $this->valueType;
    }
}
