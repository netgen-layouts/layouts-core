<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Config\ConfigAwareStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareStructTrait;

final class ItemCreateStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;

    /**
     * The definition of the item which will be created.
     *
     * Required.
     *
     * @var \Netgen\Layouts\Collection\Item\ItemDefinitionInterface
     */
    public $definition;

    /**
     * The value stored within the item.
     *
     * @var int|string|null
     */
    public $value;

    /**
     * View type which will be used to render the item.
     *
     * @var string|null
     */
    public $viewType;
}
