<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Block;

use Netgen\Layouts\Utils\HydratorTrait;

final class BlockTranslationUpdateStruct
{
    use HydratorTrait;

    /**
     * New block parameters.
     *
     * @var array<string, mixed>|null
     */
    public ?array $parameters = null;
}
