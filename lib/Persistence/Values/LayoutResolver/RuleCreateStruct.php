<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleCreateStruct
{
    use HydratorTrait;

    /**
     * Rule UUID. If specified, rule will be created with this UUID if not
     * already taken by an existing rule.
     */
    public ?string $uuid;

    /**
     * UUID of the layout mapped to new rule.
     */
    public ?string $layoutId;

    /**
     * Priority of the new rule.
     */
    public ?int $priority;

    /**
     * Flag indicating if the new rule will be enabled.
     */
    public bool $enabled;

    /**
     * Human readable description of the rule.
     */
    public string $description;

    /**
     * Rule status. One of self::STATUS_* flags.
     */
    public int $status;
}
