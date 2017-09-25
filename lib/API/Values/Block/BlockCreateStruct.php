<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\ValueObject;

final class BlockCreateStruct extends ValueObject implements ParameterStruct, ConfigAwareStruct
{
    use ParameterStructTrait;
    use ConfigAwareStructTrait;

    /**
     * Block definition to create the new block from.
     *
     * Required.
     *
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $definition;

    /**
     * View type of the new block.
     *
     * Required.
     *
     * @var string
     */
    public $viewType;

    /**
     * Item view type of the new block.
     *
     * Required.
     *
     * @var string
     */
    public $itemViewType;

    /**
     * Human readable name of the block.
     *
     * @var string
     */
    public $name;

    /**
     * Specifies if the block will be translatable.
     *
     * Required.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * Specifies if the block will be always available.
     *
     * Required.
     *
     * @var bool
     */
    public $alwaysAvailable;
}
