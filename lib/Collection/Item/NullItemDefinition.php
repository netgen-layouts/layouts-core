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
    public function __construct(string $valueType)
    {
        $this->valueType = $valueType;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }
}
