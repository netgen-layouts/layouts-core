<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class QueryTranslationUpdateStruct extends ValueObject
{
    /**
     * New parameter values for the query.
     *
     * @var array
     */
    public $parameters;
}
