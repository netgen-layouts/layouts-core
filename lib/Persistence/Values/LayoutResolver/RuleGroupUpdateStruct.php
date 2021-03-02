<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleGroupUpdateStruct
{
    use HydratorTrait;

    /**
     * New rule group name.
     */
    public ?string $name = null;

    /**
     * New human readable description of the rule group.
     */
    public ?string $description = null;
}
