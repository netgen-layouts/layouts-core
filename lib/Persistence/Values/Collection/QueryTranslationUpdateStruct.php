<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Utils\HydratorTrait;

final class QueryTranslationUpdateStruct
{
    use HydratorTrait;

    /**
     * New parameter values for the query.
     *
     * @var array|null
     */
    public $parameters;
}
