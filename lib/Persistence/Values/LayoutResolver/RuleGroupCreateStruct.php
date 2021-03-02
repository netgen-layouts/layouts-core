<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleGroupCreateStruct
{
    use HydratorTrait;

    /**
     * Rule group UUID. If specified, rule group will be created with this UUID if not
     * already taken by an existing rule group.
     */
    public ?string $uuid;

    /**
     * Name of the new rule group.
     */
    public string $name;

    /**
     * Priority of the new rule group.
     */
    public ?int $priority;

    /**
     * Flag indicating if the new rule group will be enabled.
     */
    public bool $enabled;

    /**
     * Human readable comment of the rule group.
     */
    public string $comment;

    /**
     * Rule group status. One of self::STATUS_* flags.
     */
    public int $status;
}
