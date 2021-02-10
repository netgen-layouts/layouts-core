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
     */
    public int $id;

    /**
     * Target UUID.
     */
    public string $uuid;

    /**
     * ID of the rule where this target is located.
     */
    public int $ruleId;

    /**
     * UUID of the rule where this target is located.
     */
    public string $ruleUuid;

    /**
     * Identifier of the target type.
     */
    public string $type;

    /**
     * Target value.
     *
     * @var mixed
     */
    public $value;
}
