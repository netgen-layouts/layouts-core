<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class QueryCreateStruct
{
    use HydratorTrait;

    /**
     * Identifier of the type of new query.
     */
    public string $type;

    /**
     * Parameters for the new query.
     *
     * @var array<string, mixed>
     */
    public array $parameters;
}
