<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleUpdateStruct
{
    use HydratorTrait;

    /**
     * UUID of the mapped layout. Set to "false" to remove the existing mapping.
     *
     * @var string|bool|null
     */
    public $layoutId;

    /**
     * Human readable comment of the rule.
     */
    public ?string $comment = null;
}
