<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

final class RuleGroupCreateStruct
{
    /**
     * Rule group UUID. If specified, rule group will be created with this UUID if not
     * already taken by an existing rule group.
     *
     * @var \Ramsey\Uuid\UuidInterface|null
     */
    public $uuid;

    /**
     * Priority of the rule group.
     *
     * @var int|null
     */
    public $priority;

    /**
     * Specifies if the rule group will be enabled or not.
     *
     * @var bool
     */
    public $enabled = true;

    /**
     * Description of the rule group.
     *
     * @var string|null
     */
    public $comment;
}
