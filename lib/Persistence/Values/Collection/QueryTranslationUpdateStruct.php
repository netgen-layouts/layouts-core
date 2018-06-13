<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Value;

final class QueryTranslationUpdateStruct extends Value
{
    /**
     * New parameter values for the query.
     *
     * @var array|null
     */
    public $parameters;
}
