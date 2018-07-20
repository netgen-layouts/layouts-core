<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Utils\HydratorTrait;

final class BlockTranslationUpdateStruct
{
    use HydratorTrait;

    /**
     * New block parameters.
     *
     * @var array|null
     */
    public $parameters;
}
