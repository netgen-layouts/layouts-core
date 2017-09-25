<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\ValueObject;

final class TranslationUpdateStruct extends ValueObject
{
    /**
     * New block parameters.
     *
     * @var array
     */
    public $parameters;
}
