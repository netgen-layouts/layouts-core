<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\ValueObject;

final class BlockUpdateStruct extends ValueObject implements ParameterStruct, ConfigAwareStruct
{
    use ParameterStructTrait;
    use ConfigAwareStructTrait;

    /**
     * The locale which will be updated.
     *
     * Required.
     *
     * @var string
     */
    public $locale;

    /**
     * New view type of the block.
     *
     * @var string
     */
    public $viewType;

    /**
     * New item view type of the block.
     *
     * @var string
     */
    public $itemViewType;

    /**
     * New human readable name of the block.
     *
     * @var string
     */
    public $name;

    /**
     * New state of the always available flag.
     *
     * @var bool
     */
    public $alwaysAvailable;
}
