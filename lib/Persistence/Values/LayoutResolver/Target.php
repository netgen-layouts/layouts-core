<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Target extends Value
{
    use HydratorTrait;

    /**
     * Target ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * ID of the rule where this target is located.
     *
     * @var int|string
     */
    public $ruleId;

    /**
     * Identifier of the target type.
     *
     * @var string
     */
    public $type;

    /**
     * Target value.
     *
     * @var mixed
     */
    public $value;
}
