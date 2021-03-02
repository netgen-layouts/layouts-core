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
     *
     * If not specified, rule will be placed after all other rules and rule groups at the same level.
     */
    public ?int $priority = null;

    /**
     * Specifies if the rule will be enabled or not.
     */
    public bool $enabled = true;

    /**
     * Description of the rule.
     */
    public string $description = '';

    /**
     * Description of the rule.
     *
     * @deprecated use self::$description instead
     */
    public ?string $comment = '';
}
