<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Value;

final class QueryCreateStruct extends Value
{
    /**
     * Identifier of the type of new query.
     *
     * @var string
     */
    public $type;

    /**
     * Parameters for the new query.
     *
     * @var array
     */
    public $parameters;
}
