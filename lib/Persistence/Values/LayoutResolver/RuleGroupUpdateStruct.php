<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleGroupUpdateStruct
{
    use HydratorTrait;

    /**
     * Human readable comment of the rule group.
     */
    public ?string $comment = null;
}
