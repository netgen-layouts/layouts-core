<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleUpdateStruct
{
    use HydratorTrait;

    /**
     * ID of the mapped layout. Set to 0 to remove the existing mapping.
     *
     * @var int|string|null
     */
    public $layoutId;

    /**
     * Human readable comment of the rule.
     *
     * @var string|null
     */
    public $comment;
}
