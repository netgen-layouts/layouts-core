<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\Config\ConfigDefinitionAwareInterface;

/**
 * Item definition represents the model of the collection item.
 * Currently, this only specifies what kind of configuration the item accepts.
 */
interface ItemDefinitionInterface extends ConfigDefinitionAwareInterface
{
    /**
     * Returns the value type for this definition.
     */
    public function getValueType(): string;
}
