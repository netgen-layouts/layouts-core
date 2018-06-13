<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;

final class NullItemDefinition implements ItemDefinitionInterface
{
    use ConfigDefinitionAwareTrait;

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

    public function getValueType()
    {
        return $this->valueType;
    }
}
