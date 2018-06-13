<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Value;

final class TranslationUpdateStruct extends Value
{
    /**
     * New block parameters.
     *
     * @var array|null
     */
    public $parameters;
}
