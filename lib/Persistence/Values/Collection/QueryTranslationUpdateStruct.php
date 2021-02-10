<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class QueryTranslationUpdateStruct
{
    use HydratorTrait;

    /**
     * New parameter values for the query.
     *
     * @var array<string, mixed>|null
     */
    public ?array $parameters = null;
}
