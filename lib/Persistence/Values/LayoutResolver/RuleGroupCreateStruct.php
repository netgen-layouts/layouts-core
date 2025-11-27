<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\Status;
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
     * Human readable description of the new rule group.
     */
    public string $description;

    /**
     * Priority of the new rule group.
     */
    public ?int $priority;

    /**
     * Flag indicating if the new rule group will be enabled.
     */
    public bool $isEnabled;

    /**
     * Rule group status.
     */
    public Status $status;
}
