<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Value;

final class BlockUpdateStruct extends Value
{
    /**
     * New view type of the block.
     *
     * @var string|null
     */
    public $viewType;

    /**
     * New item view type of the block.
     *
     * @var string|null
     */
    public $itemViewType;

    /**
     * New human readable name of the block.
     *
     * @var string|null
     */
    public $name;

    /**
     * Flag indicating if the block will be always available.
     *
     * @var bool|null
     */
    public $alwaysAvailable;

    /**
     * Flag indicating if the block will be translatable.
     *
     * @var bool|null
     */
    public $isTranslatable;

    /**
     * New block configuration.
     *
     * @var array|null
     */
    public $config;
}
