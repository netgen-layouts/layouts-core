<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\Config\ConfigDefinitionAwareTrait;
use Netgen\Layouts\Utils\HydratorTrait;

/**
 * @final
 */
class ItemDefinition implements ItemDefinitionInterface
{
    use ConfigDefinitionAwareTrait;
    use HydratorTrait;

    private string $valueType;

    public function getValueType(): string
    {
        return $this->valueType;
    }
}
