<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;

final class NullItemDefinition implements ItemDefinitionInterface
{
    use ConfigDefinitionAwareTrait;

    public function getValueType()
    {
        return 'null';
    }
}
