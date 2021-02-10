<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Ramsey\Uuid\UuidInterface;

final class RuleCreateStruct
{
    /**
     * Rule UUID. If specified, rule will be created with this UUID if not
     * already taken by an existing rule.
     */
    public ?UuidInterface $uuid = null;

    /**
     * The UUID of the layout to which the rule will be mapped.
     */
    public ?UuidInterface $layoutId = null;

    /**
     * Priority of the rule.
     */
    public ?int $priority = null;

    /**
     * Specifies if the rule will be enabled or not.
     */
    public bool $enabled = true;

    /**
     * Description of the rule.
     */
    public ?string $comment = '';
}
