<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Utils\HydratorTrait;

final class TranslationUpdateStruct
{
    use HydratorTrait;

    /**
     * New block parameters.
     *
     * @var array|null
     */
    public $parameters;
}
