<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\Config\ConfigDefinitionAwareTrait;

final class NullItemDefinition implements ItemDefinitionInterface
{
    use ConfigDefinitionAwareTrait;

    private string $valueType;

    public function __construct(string $valueType)
    {
        $this->valueType = $valueType;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }
}
