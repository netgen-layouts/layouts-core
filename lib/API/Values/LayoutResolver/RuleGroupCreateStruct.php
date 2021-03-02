<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Ramsey\Uuid\UuidInterface;

final class RuleGroupCreateStruct
{
    /**
     * Rule group UUID. If specified, rule group will be created with this UUID if not
     * already taken by an existing rule group.
     */
    public ?UuidInterface $uuid = null;

    /**
     * Human readable name of the rule group.
     *
     * Required.
     */
    public string $name;

    /**
     * Priority of the rule group.
     */
    public ?int $priority = null;

    /**
     * Specifies if the rule group will be enabled or not.
     */
    public bool $enabled = true;

    /**
     * Description of the rule group.
     */
    public string $comment = '';
}
